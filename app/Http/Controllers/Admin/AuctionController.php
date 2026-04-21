<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppraisalRequest;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionController extends Controller
{
    public function index()
    {
        $auctions = Auction::with(['appraisalRequest', 'bids'])
            ->latest()
            ->paginate(10);

        return view('admin.auctions.index', compact('auctions'));
    }

    public function create()
    {
        // Only completed appraisals not yet in auction
        $appraisals = AppraisalRequest::where('status', AppraisalRequest::STATUS_COMPLETED)
            ->whereDoesntHave('auction')
            ->orderBy('vehicle_brand')
            ->get();

        return view('admin.auctions.create', compact('appraisals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appraisal_request_id' => 'required|exists:appraisal_requests,id',
            'start_time'           => 'required|date|after_or_equal:now',
            'end_time'             => 'required|date|after:start_time',
            'reserve_price'        => 'nullable|numeric|min:0',
        ]);

        // Ensure the appraisal is completed and has no existing auction
        $appraisal = AppraisalRequest::where('id', $validated['appraisal_request_id'])
            ->where('status', AppraisalRequest::STATUS_COMPLETED)
            ->whereDoesntHave('auction')
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $auction = Auction::create([
                'appraisal_request_id' => $appraisal->id,
                'start_time'           => $validated['start_time'],
                'end_time'             => $validated['end_time'],
                'reserve_price'        => $validated['reserve_price'] ?? null,
                'status'               => Auction::STATUS_OPEN,
            ]);

            $appraisal->update(['status' => AppraisalRequest::STATUS_IN_AUCTION]);

            DB::commit();

            // Notify all dealers about the new auction
            $dealers = User::where('role', 'dealer')->whereNotNull('fcm_token')->get();
            if ($dealers->isNotEmpty()) {
                $tokens = $dealers->pluck('fcm_token')->toArray();
                $title = __('auctions.fcm_new_auction_title');
                $body = __('auctions.fcm_new_auction_body', [
                    'brand' => $appraisal->vehicle_brand,
                    'model' => $appraisal->vehicle_model,
                ]);
                $data = ['type' => 'new_auction', 'auction_id' => (string) $auction->id];
                FcmService::sendToTokens($tokens, $title, $body, $data);
            }

            // Also save in-app notification for each dealer
            $allDealers = User::where('role', 'dealer')->get();
            foreach ($allDealers as $dealer) {
                $dealer->notifications()->create([
                    'title' => __('auctions.fcm_new_auction_title'),
                    'body'  => __('auctions.fcm_new_auction_body', [
                        'brand' => $appraisal->vehicle_brand,
                        'model' => $appraisal->vehicle_model,
                    ]),
                    'data' => ['type' => 'new_auction', 'auction_id' => (string) $auction->id],
                ]);
            }

            return redirect()->route('auctions.index')->with('notify', [
                'type'    => 'success',
                'title'   => __('auctions.notify_published_title'),
                'message' => __('auctions.notify_published_message'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error publishing auction: ' . $e->getMessage());

            $errorDetails = app()->environment('local') ? $e->getMessage() . "\n\n" . $e->getTraceAsString() : null;

            return back()->withInput()->with('notify', [
                'type'    => 'error',
                'title'   => __('auctions.notify_publish_error_title'),
                'message' => __('auctions.notify_publish_error_message'),
                'details' => $errorDetails,
            ]);
        }
    }

    public function show(Auction $auction)
    {
        $auction->load(['appraisalRequest.photos', 'bids.dealer', 'winner']);

        $highestBid = $auction->bids()->orderByDesc('amount')->first();

        return view('admin.auctions.show', compact('auction', 'highestBid'));
    }

    public function award(Request $request, Auction $auction)
    {
        $validated = $request->validate([
            'bid_id' => 'required|exists:auction_bids,id',
        ]);

        $bid = AuctionBid::where('id', $validated['bid_id'])
            ->where('auction_id', $auction->id)
            ->firstOrFail();

        if ($auction->status === Auction::STATUS_AWARDED) {
            return back()->with('notify', [
                'type'    => 'error',
                'title'   => __('auctions.notify_award_error_title'),
                'message' => __('auctions.notify_already_awarded'),
            ]);
        }

        try {
            DB::beginTransaction();

            $auction->update([
                'status'         => Auction::STATUS_AWARDED,
                'winner_id'      => $bid->dealer_id,
                'winning_bid_id' => $bid->id,
            ]);

            $auction->appraisalRequest->update(['status' => AppraisalRequest::STATUS_ACQUIRED]);

            DB::commit();

            // Notify winning dealer
            $winner = $bid->dealer;
            if ($winner) {
                $appraisal = $auction->appraisalRequest;
                $title = __('auctions.fcm_won_title');
                $body = __('auctions.fcm_won_body', [
                    'brand' => $appraisal->vehicle_brand,
                    'model' => $appraisal->vehicle_model,
                ]);
                $data = [
                    'type'       => 'auction_won',
                    'auction_id' => (string) $auction->id,
                ];

                $winner->notifications()->create(['title' => $title, 'body' => $body, 'data' => $data]);

                if ($winner->fcm_token) {
                    FcmService::sendToToken($winner->fcm_token, $title, $body, $data);
                }
            }

            // Notify other bidding dealers that auction is closed
            $losingBids = $auction->bids()->where('id', '!=', $bid->id)->with('dealer')->get();
            foreach ($losingBids as $losingBid) {
                $dealer = $losingBid->dealer;
                if (!$dealer) continue;

                $appraisal = $auction->appraisalRequest;
                $title = __('auctions.fcm_closed_title');
                $body  = __('auctions.fcm_closed_body', [
                    'brand' => $appraisal->vehicle_brand,
                    'model' => $appraisal->vehicle_model,
                ]);
                $data = ['type' => 'auction_closed', 'auction_id' => (string) $auction->id];

                $dealer->notifications()->create(['title' => $title, 'body' => $body, 'data' => $data]);

                if ($dealer->fcm_token) {
                    FcmService::sendToToken($dealer->fcm_token, $title, $body, $data);
                }
            }

            return redirect()->route('auctions.show', $auction)->with('notify', [
                'type'    => 'success',
                'title'   => __('auctions.notify_awarded_title'),
                'message' => __('auctions.notify_awarded_message'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error awarding auction: ' . $e->getMessage());

            $errorDetails = app()->environment('local') ? $e->getMessage() . "\n\n" . $e->getTraceAsString() : null;

            return back()->with('notify', [
                'type'    => 'error',
                'title'   => __('auctions.notify_award_error_title'),
                'message' => __('auctions.notify_award_error_message'),
                'details' => $errorDetails,
            ]);
        }
    }
}
