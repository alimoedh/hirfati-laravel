<?php

namespace App\Services;

use App\Models\{Warranty, Request, Setting};

class WarrantyService
{
    public static function create(int $requestId, ?int $days = null): Warranty
    {
        $days      = $days ?? (int) Setting::get('warranty_days', 30);
        $startDate = now();
        $endDate   = now()->addDays($days);

        $warranty = Warranty::create([
            'request_id' => $requestId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'status'     => 'active',
        ]);

        Request::where('id', $requestId)->update([
            'has_warranty'      => true,
            'warranty_end_date' => $endDate,
        ]);

        return $warranty;
    }
}
