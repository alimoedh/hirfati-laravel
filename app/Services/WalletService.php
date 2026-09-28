<?php

namespace App\Services;

use App\Models\{CraftsmanWallet, WalletTransaction, Setting};
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    public static function addToEscrow(int $craftsmanId, int $requestId, float $amount): bool
    {
        if ($amount <= 0) return false;

        return DB::transaction(function () use ($craftsmanId, $requestId, $amount) {
            $wallet = CraftsmanWallet::forUser($craftsmanId);
            $wallet->increment('escrow_balance', $amount);
            $wallet->refresh();

            WalletTransaction::create([
                'craftsman_id'  => $craftsmanId,
                'request_id'    => $requestId,
                'type'          => 'escrow_in',
                'amount'        => $amount,
                'balance_after' => $wallet->escrow_balance,
                'description'   => "دفعة معلقة للطلب #" . str_pad($requestId, 4, '0', STR_PAD_LEFT),
                'status'        => 'pending',
            ]);

            return true;
        });
    }

    public static function releaseFromEscrow(int $craftsmanId, int $requestId, float $amount, ?float $commissionPercent = null): bool
    {
        if ($amount <= 0) return false;

        $commissionPercent = $commissionPercent ?? (float) Setting::get('commission_percentage', 5);

        return DB::transaction(function () use ($craftsmanId, $requestId, $amount, $commissionPercent) {
            $wallet = CraftsmanWallet::forUser($craftsmanId);

            if ($wallet->escrow_balance < $amount) {
                throw new Exception('الرصيد المعلق غير كافٍ');
            }

            $commission = round($amount * ($commissionPercent / 100), 2);
            $netAmount  = $amount - $commission;

            $wallet->decrement('escrow_balance', $amount);
            $wallet->increment('balance', $netAmount);
            $wallet->increment('total_earned', $amount);
            $wallet->increment('total_commission', $commission);
            $wallet->refresh();

            WalletTransaction::create([
                'craftsman_id'  => $craftsmanId,
                'request_id'    => $requestId,
                'type'          => 'escrow_release',
                'amount'        => $netAmount,
                'balance_after' => $wallet->balance,
                'description'   => "تحرير دفعة الطلب #" . str_pad($requestId, 4, '0', STR_PAD_LEFT) . " (بعد خصم $commissionPercent%)",
                'status'        => 'completed',
            ]);

            WalletTransaction::create([
                'craftsman_id'  => $craftsmanId,
                'request_id'    => $requestId,
                'type'          => 'commission',
                'amount'        => $commission,
                'balance_after' => $wallet->balance,
                'description'   => "عمولة المنصة ($commissionPercent%) للطلب #" . str_pad($requestId, 4, '0', STR_PAD_LEFT),
                'status'        => 'completed',
            ]);

            NotificationService::send(
                $craftsmanId,
                '💰 تم إضافة أرباح لرصيدك',
                "تم إضافة " . number_format($netAmount) . " ريال لرصيدك من الطلب #" . str_pad($requestId, 4, '0', STR_PAD_LEFT),
                config('hirfati.site_url') . '/craftsman/commissions',
                'success'
            );

            return true;
        });
    }

    public static function withdraw(int $craftsmanId, float $amount, string $method = 'bank'): array
    {
        $wallet        = CraftsmanWallet::forUser($craftsmanId);
        $minWithdrawal = (float) Setting::get('min_withdrawal', 1000);

        if ($amount <= 0) {
            return ['success' => false, 'error' => 'المبلغ غير صالح'];
        }
        if ($amount > $wallet->balance) {
            return ['success' => false, 'error' => 'الرصيد غير كافٍ'];
        }
        if ($amount < $minWithdrawal) {
            return ['success' => false, 'error' => "الحد الأدنى للسحب هو " . number_format($minWithdrawal) . " ريال"];
        }

        return DB::transaction(function () use ($wallet, $craftsmanId, $amount, $method) {
            $wallet->decrement('balance', $amount);
            $wallet->increment('total_withdrawn', $amount);
            $wallet->refresh();

            $methodNames = ['bank' => 'حساب بنكي', 'wallet' => 'محفظة إلكترونية', 'cash' => 'نقداً'];

            WalletTransaction::create([
                'craftsman_id'  => $craftsmanId,
                'type'          => 'withdraw',
                'amount'        => $amount,
                'balance_after' => $wallet->balance,
                'description'   => "سحب مبلغ عبر " . ($methodNames[$method] ?? 'طريقة أخرى'),
                'status'        => 'completed',
            ]);

            NotificationService::send(
                $craftsmanId,
                '✅ تم تسجيل طلب السحب',
                "تم خصم " . number_format($amount) . " ريال من رصيدك — سيتم التحويل خلال 3 أيام عمل",
                config('hirfati.site_url') . '/craftsman/commissions',
                'info'
            );

            return ['success' => true];
        });
    }

    public static function getStats(int $craftsmanId): array
    {
        $wallet = CraftsmanWallet::forUser($craftsmanId);

        return [
            'balance'          => (float) $wallet->balance,
            'escrow'           => (float) $wallet->escrow_balance,
            'total_earned'     => (float) $wallet->total_earned,
            'total_commission' => (float) $wallet->total_commission,
            'total_withdrawn'  => (float) $wallet->total_withdrawn,
        ];
    }

    public static function getTransactions(int $craftsmanId, int $limit = 20)
    {
        return WalletTransaction::with('request:id,title')
            ->where('craftsman_id', $craftsmanId)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
