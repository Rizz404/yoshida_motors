<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\AppraisalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BidController extends Controller
{
    public function index()
    {
        $bids = AuctionBid::with(['auction.appraisalRequest'])
            ->where('dealer_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('dealer.bids.index', compact('bids'));
    }

    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $auction = Auction::with('appraisalRequest')
            ->where('id', $id)
            ->firstOrFail();

        // Ensure auction is still open for bidding
        if (!$auction->isOpen()) {
            return back()->with('notify', [
                'type'    => 'error',
                'title'   => 'Bid Failed',
                'message' => 'This auction is no longer accepting bids.',
            ]);
        }

        // Ensure dealer hasn't already bid
        $existingBid = AuctionBid::where('auction_id', $auction->id)
            ->where('dealer_id', Auth::id())
            ->first();

        if ($existingBid) {
            return back()->with('notify', [
                'type'    => 'error',
                'title'   => 'Bid Failed',
                'message' => 'You have already placed a bid on this vehicle.',
            ]);
        }

        try {
            DB::beginTransaction();

            AuctionBid::create([
                'auction_id' => $auction->id,
                'dealer_id'  => Auth::id(),
                'amount'     => $validated['amount'],
            ]);

            DB::commit();

            return back()->with('notify', [
                'type'    => 'success',
                'title'   => 'Bid Placed',
                'message' => 'Your bid of ¥' . number_format($validated['amount']) . ' has been recorded.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error placing bid: ' . $e->getMessage());

            return back()->with('notify', [
                'type'    => 'error',
                'title'   => 'Bid Failed',
                'message' => 'An error occurred while placing your bid. Please try again.',
            ]);
        }
    }
}
