<?php

namespace App\Services;

use App\Models\{Request, Installment};
use Exception;

class RequestService
{
    public static function accept(Request $request, int $craftsmanId): void
    {
        if (!$request->isPending()) {
            throw new Exception('لا يمكن قبول هذا الطلب في حالته الحالية');
        }

        $request->update([
            'status'       => 'accepted',
            'craftsman_id' => $craftsmanId,
        ]);

        NotificationService::send(
            $request->client_id,
            '✅ تم قبول طلبك',
            "قبل " . auth()->user()->full_name . " طلبك رقم #" . str_pad($request->id, 4, '0', STR_PAD_LEFT),
            url("/client/request-details/{$request->id}"),
            'success'
        );

        ActivityService::log($craftsmanId, 'accept_request', "قبول الطلب #{$request->id}");
    }

    public static function reject(Request $request, int $craftsmanId): void
    {
        if (!$request->isPending()) {
            throw new Exception('لا يمكن رفض هذا الطلب');
        }

        $request->update([
            'status'       => 'cancelled',
            'craftsman_id' => $craftsmanId,
        ]);

        NotificationService::send(
            $request->client_id,
            '❌ تم رفض طلبك',
            "رفض " . auth()->user()->full_name . " طلبك رقم #" . str_pad($request->id, 4, '0', STR_PAD_LEFT),
            url("/client/dashboard"),
            'danger'
        );

        ActivityService::log($craftsmanId, 'reject_request', "رفض الطلب #{$request->id}");
    }

    public static function start(Request $request, int $craftsmanId): array
    {
        if (!$request->isAccepted()) {
            return ['success' => false, 'error' => 'لا يمكن بدء التنفيذ في هذه المرحلة'];
        }

        if ($request->use_installment && $request->installment_count > 0) {
            $total = Installment::where('request_id', $request->id)->count();
            $paid  = Installment::where('request_id', $request->id)->where('status', 'paid')->count();

            if ($total > 0 && $paid < $total) {
                $remaining = $total - $paid;
                return [
                    'success' => false,
                    'error'   => "⚠️ لا يمكن بدء التنفيذ — يجب أن يُكمل العميل دفع جميع الأقساط. المتبقي: {$remaining} قسط",
                ];
            }
        }

        $request->update(['status' => 'in_progress']);

        NotificationService::send(
            $request->client_id,
            '⚙️ بدء تنفيذ طلبك',
            "بدأ " . auth()->user()->full_name . " تنفيذ طلبك رقم #" . str_pad($request->id, 4, '0', STR_PAD_LEFT),
            url("/client/request-details/{$request->id}"),
            'info'
        );

        ActivityService::log($craftsmanId, 'start_request', "بدء الطلب #{$request->id}");

        return ['success' => true];
    }

    public static function complete(Request $request, int $craftsmanId): void
    {
        if (!$request->isInProgress()) {
            throw new Exception('لا يمكن إكمال الخدمة');
        }

        $request->update(['status' => 'completed']);

        NotificationService::send(
            $request->client_id,
            '🎉 تم إكمال طلبك',
            "أكمل " . auth()->user()->full_name . " طلبك رقم #" . str_pad($request->id, 4, '0', STR_PAD_LEFT) . " بنجاح",
            url("/client/rate-craftsman/{$request->id}"),
            'success'
        );

        LoyaltyService::add($request->client_id, 10, 'request_completed', 'إكمال طلب خدمة');

        $amount = (float) ($request->budget ?? 0);
        if ($amount > 0) {
            WalletService::releaseFromEscrow($craftsmanId, $request->id, $amount);
        }

        ActivityService::log($craftsmanId, 'complete_request', "إكمال الطلب #{$request->id}");
    }
}
