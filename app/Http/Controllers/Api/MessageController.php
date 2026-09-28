<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as ServiceRequest;
use App\Services\{ChatService, ActivityService};
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, int $requestId)
    {
        $serviceRequest = ServiceRequest::findOrFail($requestId);
        $this->authorizeRequest($serviceRequest);

        $lastId = (int) $request->input('last_id', 0);
        $messages = ChatService::getMessages($requestId, auth()->id(), $lastId);

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'request_id'  => 'required|integer|exists:requests,id',
            'receiver_id' => 'required|integer|exists:users,id',
            'message'     => 'required|string|max:2000',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($data['request_id']);
        $this->authorizeRequest($serviceRequest);

        $message = ChatService::sendMessage([
            'request_id'  => $data['request_id'],
            'sender_id'   => auth()->id(),
            'receiver_id' => $data['receiver_id'],
            'message'     => $data['message'],
        ]);

        ActivityService::log(auth()->id(), 'send_message', "إرسال رسالة في الطلب #{$data['request_id']}");

        return response()->json(['success' => true, 'message_id' => $message->id]);
    }

    public function storeVoice(Request $request)
    {
        $data = $request->validate([
            'request_id'  => 'required|integer|exists:requests,id',
            'receiver_id' => 'required|integer|exists:users,id',
            'voice'       => 'required|file|mimes:webm,mp3,wav,ogg,m4a|max:10240',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($data['request_id']);
        $this->authorizeRequest($serviceRequest);

        $message = ChatService::sendVoice([
            'request_id'  => $data['request_id'],
            'sender_id'   => auth()->id(),
            'receiver_id' => $data['receiver_id'],
        ], $request->file('voice'));

        ActivityService::log(auth()->id(), 'send_voice', "إرسال رسالة صوتية في الطلب #{$data['request_id']}");

        return response()->json(['success' => true, 'message_id' => $message->id]);
    }

    private function authorizeRequest(ServiceRequest $request): void
    {
        $userId = auth()->id();
        abort_unless(
            $request->client_id === $userId || $request->craftsman_id === $userId,
            403, 'غير مصرح'
        );
    }
}
