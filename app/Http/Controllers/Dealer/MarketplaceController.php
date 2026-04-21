<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\AppraisalRequest;
use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    public function index()
    {
        $vehicles = AppraisalRequest::with('photos')
            ->where('status', AppraisalRequest::STATUS_COMPLETED)
            ->latest()
            ->paginate(12);

        $auctions = Auction::with(['appraisalRequest.photos'])
            ->where('status', Auction::STATUS_OPEN)
            ->latest()
            ->paginate(12, ['*'], 'auction_page');

        return view('dealer.marketplace.index', compact('vehicles', 'auctions'));
    }

    public function show($id)
    {
        // Allow viewing both completed and in_auction vehicles
        $vehicle = AppraisalRequest::with(['photos', 'auction'])
            ->whereIn('status', [
                AppraisalRequest::STATUS_COMPLETED,
                AppraisalRequest::STATUS_IN_AUCTION,
            ])
            ->findOrFail($id);

        $auction    = $vehicle->auction;
        $dealerBid  = null;

        if ($auction) {
            $dealerBid = AuctionBid::where('auction_id', $auction->id)
                ->where('dealer_id', Auth::id())
                ->first();
        }

        return view('dealer.marketplace.show', compact('vehicle', 'auction', 'dealerBid'));
    }
}
