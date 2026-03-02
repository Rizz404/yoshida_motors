<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index(Request $request)
    {
        // Admin can see their own notifications
        $notifications = $request->user()->notifications()
            ->latest()
            ->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Display the specified notification.
     */
    public function show(Request $request, Notification $notification)
    {
        // Ensure the user owns this notification
        if ($notification->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        // Mark as read when opened
        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $notification->update(['is_read' => true]);

        return back()->with('notify', [
            'type' => 'success',
            'title' => __('notifications.notify_read_title'),
            'message' => __('notifications.notify_read_message'),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('notify', [
            'type' => 'success',
            'title' => __('notifications.notify_all_read_title'),
            'message' => __('notifications.notify_all_read_message'),
        ]);
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notification->delete();

            return redirect()->route('notifications.index')->with('notify', [
                'type' => 'success',
                'title' => __('notifications.notify_deleted_title'),
                'message' => __('notifications.notify_deleted_message'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());

            return back()->with('notify', [
                'type' => 'error',
                'title' => __('notifications.notify_delete_error_title'),
                'message' => __('notifications.notify_delete_error_message'),
            ]);
        }
    }
}
