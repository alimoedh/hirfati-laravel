<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Request, Message};
use App\Services\{ChatService, ActivityService};
use Illuminate\Http\Request as HttpRequest;

class ChatController extends Controller
{
    public function index(int $requestId)
    {
        $request = Request::with(['client', 'craftsman'])->findOrFail($requestId);
        $user = auth()->user();

        $isOwner = ($user->isClient() && $request->client_id === $user->id)
                || ($user->isCraftsman() && $request->craftsman_id === $user->id)
                || $user->isAdmin();

        abort_unless($isOwner, 403);

        if ($user->isClient()) {
            $otherUser = $request->craftsman;
            $otherUserId = $request->craftsman_id;
            $otherName   = $request->craftsman->full_name ?? 'الحرفي';
        } else {
            $otherUser = $request->client;
            $otherUserId = $request->client_id;
            $otherName   = $request->client->full_name ?? 'العميل';
        }

        abort_unless($otherUserId, 404, 'لا يوجد طرف آخر في المحادثة');

        $messages = ChatService::getMessages($request->id, $user->id);

        return view('client.chat', compact('request', 'messages', 'otherUserId', 'otherName', 'otherUser'));
    }

    public function conversations()
    {
        $user = auth()->user();

        $query = Request::with(['craftsman', 'client'])
            ->whereNotNull('craftsman_id');

        if ($user->isClient()) {
            $query->where('client_id', $user->id);
        } else {
            $query->where('craftsman_id', $user->id);
        }

        $requests = $query->get()
            ->map(function ($req) use ($user) {
                $lastMessage = Message::where('request_id', $req->id)->latest()->first();
                $unread = Message::where('request_id', $req->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                return (object) [
                    'request'      => $req,
                    'last_message' => $lastMessage,
                    'unread_count' => $unread,
                ];
            })
            ->sortByDesc(fn($c) => $c->last_message?->created_at ?? $c->request->updated_at);

        return view('client.messages', compact('requests'));
    }

    public function send(HttpRequest $request)
    {
        $data = $request->validate([
            'request_id'  => 'required|integer|exists:requests,id',
            'receiver_id' => 'required|integer|exists:users,id',
            'message'     => 'required|string|max:2000',
        ]);

        $serviceRequest = Request::findOrFail($data['request_id']);
        $user = auth()->user();

        $canSend = ($user->isClient() && $serviceRequest->client_id === $user->id)
                || ($user->isCraftsman() && $serviceRequest->craftsman_id === $user->id);

        abort_unless($canSend, 403);

        $message = ChatService::sendMessage([
            'request_id'  => $data['request_id'],
            'sender_id'   => $user->id,
            'receiver_id' => $data['receiver_id'],
            'message'     => $data['message'],
        ]);

        ActivityService::log($user->id, 'send_message', "إرسال رسالة في الطلب #{$data['request_id']}");

        return response()->json(['success' => true, 'message_id' => $message->id]);
    }

    public function sendVoice(HttpRequest $request)
    {
        $data = $request->validate([
            'request_id'  => 'required|integer|exists:requests,id',
            'receiver_id' => 'required|integer|exists:users,id',
            'voice'       => 'required|file|mimes:webm,mp3,wav,ogg,m4a|max:10240',
        ]);

        $serviceRequest = Request::findOrFail($data['request_id']);
        $user = auth()->user();

        $canSend = ($user->isClient() && $serviceRequest->client_id === $user->id)
                || ($user->isCraftsman() && $serviceRequest->craftsman_id === $user->id);

        abort_unless($canSend, 403);

        $message = ChatService::sendVoice([
            'request_id'  => $data['request_id'],
            'sender_id'   => $user->id,
            'receiver_id' => $data['receiver_id'],
        ], $request->file('voice'));

        ActivityService::log($user->id, 'send_voice', "إرسال رسالة صوتية في الطلب #{$data['request_id']}");

        return response()->json(['success' => true, 'message_id' => $message->id]);
    }

    public function fetch(HttpRequest $request, int $requestId)
    {
        $user = auth()->user();
        $serviceRequest = Request::findOrFail($requestId);

        $canView = ($user->isClient() && $serviceRequest->client_id === $user->id)
                || ($user->isCraftsman() && $serviceRequest->craftsman_id === $user->id);

        abort_unless($canView, 403);

        $lastId = (int) $request->input('last_id', 0);
        $messages = ChatService::getMessages($requestId, $user->id, $lastId);

        $formatted = $messages->map(fn($m) => [
            'id'          => $m->id,
            'message'     => nl2br(e($m->message)),
            'sender_name' => $m->sender->full_name ?? 'مستخدم',
            'sender_id'   => $m->sender_id,
            'is_voice'    => $m->is_voice ? 1 : 0,
            'voice_url'   => $m->voice_url,
            'time_ago'    => $m->created_at->diffForHumans(),
        ]);

        return response()->json(['messages' => $formatted]);
    }
}
