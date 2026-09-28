<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Http\UploadedFile;

class ChatService
{
    public static function sendMessage(array $data): Message
    {
        return Message::create([
            'request_id'  => $data['request_id'],
            'sender_id'   => $data['sender_id'],
            'receiver_id' => $data['receiver_id'],
            'message'     => $data['message'],
            'is_voice'    => false,
        ]);
    }

    public static function sendVoice(array $data, UploadedFile $file): Message
    {
        $path = $file->store('voices', 'public');

        return Message::create([
            'request_id'  => $data['request_id'],
            'sender_id'   => $data['sender_id'],
            'receiver_id' => $data['receiver_id'],
            'message'     => 'رسالة صوتية',
            'is_voice'    => true,
            'voice_url'   => $path,
        ]);
    }

    public static function getMessages(int $requestId, int $userId, int $lastId = 0)
    {
        $messages = Message::with('sender:id,full_name')
            ->where('request_id', $requestId)
            ->where(fn($q) => $q->where('sender_id', $userId)->orWhere('receiver_id', $userId))
            ->when($lastId > 0, fn($q) => $q->where('id', '>', $lastId))
            ->orderBy('created_at')
            ->get();

        Message::where('receiver_id', $userId)
            ->where('request_id', $requestId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $messages;
    }
}
