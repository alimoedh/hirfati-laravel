<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $limit  = min((int) $request->input('limit', 10), 50);

        $notifications = Notification::where('user_id', $userId)
            ->latest()->limit($limit)->get();

        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'notifications' => $notifications]);
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)->count();

        return response()->json(['count' => $count]);
    }
}
