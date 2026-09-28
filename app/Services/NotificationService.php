<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public static function send(int $userId, string $title, string $message, ?string $link = null, string $type = 'info'): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'type'    => $type,
        ]);
    }

    public static function sendMany(array $userIds, string $title, string $message, ?string $link = null, string $type = 'info'): void
    {
        foreach ($userIds as $id) {
            self::send($id, $title, $message, $link, $type);
        }
    }
}
