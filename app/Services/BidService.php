<?php

namespace App\Services;

use App\Models\{Bid, Request};
use Illuminate\Support\Facades\DB;
use Exception;

class BidService
{
    /**
     * تقديم عرض من حرفي
     */
    public static function submit(Request $request, int $craftsmanId, array $data): Bid
    {
        // التحقق من أن الطلب لا يزال مفتوحاً
        if (!$request->isOpenForBidding()) {
            throw new Exception('هذا الطلب لم يعد متاحاً لتقديم العروض');
        }

        // التحقق: هل قدم هذا الحرفي عرضاً مسبقاً؟
        $existing = Bid::where('request_id', $request->id)
            ->where('craftsman_id', $craftsmanId)
            ->first();

        if ($existing) {
            throw new Exception('لقد قدمت عرضاً مسبقاً على هذا الطلب');
        }

        $bid = Bid::create([
            'request_id'    => $request->id,
            'craftsman_id'  => $craftsmanId,
            'amount'        => $data['amount'],
            'duration_days' => $data['duration_days'],
            'message'       => $data['message'] ?? null,
            'status'        => 'pending',
        ]);

        // إشعار العميل
        NotificationService::send(
            $request->client_id,
            '📨 عرض جديد على طلبك',
            "قدم " . auth()->user()->full_name . " عرضاً بقيمة " . number_format($data['amount']) . " ريال",
            url("/client/request-details/{$request->id}"),
            'info'
        );

        ActivityService::log($craftsmanId, 'submit_bid', "تقديم عرض على الطلب #{$request->id}");

        return $bid;
    }

    /**
     * تعديل عرض
     */
    public static function update(Bid $bid, array $data): Bid
    {
        if (!$bid->isPending()) {
            throw new Exception('لا يمكن تعديل هذا العرض');
        }

        if ($bid->craftsman_id !== auth()->id()) {
            throw new Exception('غير مصرح');
        }

        $bid->update([
            'amount'        => $data['amount'],
            'duration_days' => $data['duration_days'],
            'message'       => $data['message'] ?? $bid->message,
        ]);

        ActivityService::log(auth()->id(), 'update_bid', "تعديل العرض #{$bid->id}");

        return $bid->fresh();
    }

    /**
     * سحب عرض (من الحرفي)
     */
    public static function withdraw(Bid $bid): void
    {
        if (!$bid->isPending()) {
            throw new Exception('لا يمكن سحب هذا العرض');
        }

        if ($bid->craftsman_id !== auth()->id()) {
            throw new Exception('غير مصرح');
        }

        $bid->update(['status' => 'withdrawn']);

        ActivityService::log(auth()->id(), 'withdraw_bid', "سحب العرض #{$bid->id}");
    }

    /**
     * قبول عرض (من العميل)
     */
    public static function accept(Bid $bid): void
    {
        if (!$bid->isPending()) {
            throw new Exception('هذا العرض لم يعد متاحاً');
        }

        $request = $bid->request;

        if ($request->client_id !== auth()->id()) {
            throw new Exception('غير مصرح');
        }

        if ($request->craftsman_id) {
            throw new Exception('تم اختيار حرفي مسبقاً لهذا الطلب');
        }

        DB::transaction(function () use ($bid, $request) {
            // قبول هذا العرض
            $bid->update(['status' => 'accepted']);

            // رفض باقي العروض
            Bid::where('request_id', $request->id)
                ->where('id', '!=', $bid->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            // ربط الحرفي بالطلب وتحديد السعر
            $request->update([
                'craftsman_id' => $bid->craftsman_id,
                'budget'       => $bid->amount,
            ]);
        });

        // إشعار الحرفي المقبول
        NotificationService::send(
            $bid->craftsman_id,
            '🎉 تم قبول عرضك',
            "قبل العميل عرضك بقيمة " . number_format($bid->amount) . " ريال — سيتم الدفع خلال وقت قريب",
            url("/craftsman/dashboard"),
            'success'
        );

        // إشعار باقي الحرفيين بالرفض
        $rejectedBids = Bid::where('request_id', $request->id)
            ->where('status', 'rejected')
            ->get();

        foreach ($rejectedBids as $rejected) {
            NotificationService::send(
                $rejected->craftsman_id,
                '❌ لم يتم اختيار عرضك',
                "تم اختيار حرفي آخر للطلب #" . str_pad($request->id, 4, '0', STR_PAD_LEFT),
                url("/craftsman/dashboard"),
                'warning'
            );
        }

        ActivityService::log(auth()->id(), 'accept_bid', "قبول العرض #{$bid->id} للطلب #{$request->id}");
    }

    /**
     * رفض عرض (من العميل)
     */
    public static function reject(Bid $bid): void
    {
        if (!$bid->isPending()) {
            throw new Exception('لا يمكن رفض هذا العرض');
        }

        if ($bid->request->client_id !== auth()->id()) {
            throw new Exception('غير مصرح');
        }

        $bid->update(['status' => 'rejected']);

        NotificationService::send(
            $bid->craftsman_id,
            '❌ تم رفض عرضك',
            "رفض العميل عرضك على الطلب #" . str_pad($bid->request_id, 4, '0', STR_PAD_LEFT),
            url("/craftsman/dashboard"),
            'warning'
        );

        ActivityService::log(auth()->id(), 'reject_bid', "رفض العرض #{$bid->id}");
    }
}
