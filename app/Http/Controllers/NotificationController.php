<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display notifications page
     */
    public function index()
    {
        $notifications = $this->notificationService->getAll(auth()->user(), 20);
        $unreadCount = $this->notificationService->getUnreadCount(auth()->user());

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get unread notifications (for dropdown/widget)
     */
    public function unread()
    {
        $notifications = $this->notificationService->getUnread(auth()->user(), 10);
        $unreadCount = $this->notificationService->getUnreadCount(auth()->user());

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $this->notificationService->markAsRead($notification);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $count = $this->notificationService->markAllAsRead(auth()->user());

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        }

        return redirect()->back()->with('success', "تم تعليم {$count} إشعار كمقروء");
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $this->notificationService->delete($notification);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم حذف الإشعار');
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead()
    {
        $count = $this->notificationService->deleteAllRead(auth()->user());

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        }

        return redirect()->back()->with('success', "تم حذف {$count} إشعار");
    }
}
