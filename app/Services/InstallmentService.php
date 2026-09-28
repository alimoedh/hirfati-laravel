<?php

namespace App\Services;

use App\Models\Installment;

class InstallmentService
{
    public static function createSchedule(int $requestId, float $totalAmount, int $count = 3): void
    {
        for ($i = 1; $i <= $count; $i++) {
            Installment::create([
                'request_id'          => $requestId,
                'total_amount'        => $totalAmount,
                'paid_amount'         => 0,
                'remaining_amount'    => $totalAmount,
                'installment_count'   => $count,
                'current_installment' => $i,
                'due_date'            => now()->addMonths($i - 1)->toDateString(),
                'status'              => 'pending',
            ]);
        }
    }

    public static function pay(Installment $installment): bool
    {
        $perAmount = $installment->per_installment_amount;

        $installment->update([
            'paid_amount'      => $installment->paid_amount + $perAmount,
            'remaining_amount' => max(0, $installment->remaining_amount - $perAmount),
            'status'           => 'paid',
        ]);

        return true;
    }

    public static function areAllPaid(int $requestId): bool
    {
        $total = Installment::where('request_id', $requestId)->count();
        $paid  = Installment::where('request_id', $requestId)->where('status', 'paid')->count();

        return $total > 0 && $paid >= $total;
    }
}
