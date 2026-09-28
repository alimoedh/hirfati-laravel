<?php

namespace App\Services;

use App\Models\SystemLog;

class ActivityService
{
    public static function log(?int $userId, string $action, ?string $details = null): void
    {
        SystemLog::create([
            'user_id'    => $userId ?? auth()->id(),
            'action'     => $action,
            'details'    => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
