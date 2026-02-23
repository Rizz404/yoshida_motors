<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $notifications = $request->user()->notifications()
            ->latest()
            ->cursorPaginate($perPage);

        return $this->cursorPaginatedResponse($notifications, 'Notifications retrieved successfully');
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);

        if (!$notification) {
            return $this->notFoundResponse('Notification not found');
        }

        $notification->update(['is_read' => true]);

        return $this->successResponse($notification, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->successResponse(null, 'All notifications marked as read');
    }
}
