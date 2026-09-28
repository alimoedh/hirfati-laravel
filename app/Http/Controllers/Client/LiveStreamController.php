<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Request;

class LiveStreamController extends Controller
{
    public function show(?int $requestId = null)
    {
        $user = auth()->user();
        $request = null;

        if ($requestId) {
            $request = Request::with(['client', 'craftsman'])->find($requestId);

            if ($request) {
                $isOwner = ($user->isClient() && $request->client_id === $user->id)
                        || ($user->isCraftsman() && $request->craftsman_id === $user->id)
                        || $user->isAdmin();
                if (!$isOwner) $request = null;
            }
        }

        $roomName = $requestId ? 'hirfati-req-' . $requestId : 'hirfati-lobby';

        $backUrl = match($user->role) {
            'craftsman' => route('craftsman.dashboard'),
            'admin'     => route('admin.dashboard'),
            default     => route('client.dashboard'),
        };

        return view('client.live-stream', compact('request', 'roomName', 'backUrl'));
    }
}
