<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Request, Message, Notification};

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $requests = Request::with(['category', 'craftsman'])
            ->where('client_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $activeRequest = $requests->first(fn($r) =>
            in_array($r->status, ['pending', 'accepted', 'in_progress'])
        );

        $notifications = Notification::where('user_id', $user->id)
            ->latest()->limit(5)->get();

        $unreadMessages = Message::where('receiver_id', $user->id)
            ->where('is_read', false)->count();

        $stats = [
            'total'       => $requests->count(),
            'completed'   => $requests->where('status', 'completed')->count(),
            'pending'     => $requests->where('status', 'pending')->count(),
            'in_progress' => $requests->where('status', 'in_progress')->count(),
        ];

        return view('client.dashboard', compact(
            'user', 'requests', 'activeRequest',
            'notifications', 'unreadMessages', 'stats'
        ));
    }
}
