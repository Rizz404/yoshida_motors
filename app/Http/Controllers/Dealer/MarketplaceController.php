<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppraisalRequest;

class MarketplaceController extends Controller
{
    /**
     * Display a listing of completed appraisals available for bidding.
     */
    public function index()
    {
        // For Phase 2, we list vehicles that have been marked as completed/ready for purchase.
        // As defined in END_TO_END_SYSTEM_WORKFLOW: "They only see a catalog of vehicles ready for purchase"
        $vehicles = AppraisalRequest::with('photos')
            ->where('status', 'completed')
            ->latest()
            ->paginate(12);

        return view('dealer.marketplace.index', compact('vehicles'));
    }

    /**
     * Display the specified vehicle details.
     */
    public function show($id)
    {
        $vehicle = AppraisalRequest::with('photos')
            ->where('status', 'completed')
            ->findOrFail($id);

        return view('dealer.marketplace.show', compact('vehicle'));
    }
}
