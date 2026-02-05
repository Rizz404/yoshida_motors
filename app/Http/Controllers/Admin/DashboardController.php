<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppraisalRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Status: 'draft', 'submitted', 'under_review', 'completed'

        // 1. Total Requests (Semua kendaraan yang masuk)
        $totalVehicles = AppraisalRequest::count();

        // 2. Pending Reviews (Yang statusnya 'submitted' menunggu admin)
        $pendingReviews = AppraisalRequest::where('status', 'submitted')->count();

        // 3. In Progress (Yang statusnya 'under_review')
        // Aku ganti 'Reported Issues' jadi ini ya, biar lebih pas sama workflow kamu!
        $underReview = AppraisalRequest::where('status', 'under_review')->count();

        // 4. Total Appraised Value (Total harga 'final_price' dari yang sudah 'completed')
        // Ini pengganti 'Total Revenue' biar sesuai konteks appraisal ya, Kak!
        $totalValue = AppraisalRequest::where('status', 'completed')->sum('final_price');

        // 5. Recent Submissions (Ambil 5 data terbaru + usernya)
        $recentRequests = AppraisalRequest::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalVehicles',
            'pendingReviews',
            'underReview',
            'totalValue',
            'recentRequests'
        ));
    }
}
