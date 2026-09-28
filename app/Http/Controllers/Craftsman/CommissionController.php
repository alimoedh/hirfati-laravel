<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\WalletService;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $wallet = WalletService::getStats($user->id);
        $transactions = WalletService::getTransactions($user->id, 20);

        $minWithdrawal = (float) Setting::get('min_withdrawal', 1000);
        $commissionPercentage = (float) Setting::get('commission_percentage', 5);

        return view('craftsman.commissions', compact(
            'user', 'wallet', 'transactions',
            'minWithdrawal', 'commissionPercentage'
        ));
    }

    public function withdraw(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:bank,wallet,cash',
        ]);

        $result = WalletService::withdraw(auth()->id(), (float) $data['amount'], $data['method']);

        if ($result['success']) {
            return back()->with('success', '✅ تم تسجيل طلب السحب بنجاح — سيتم التحويل خلال 3 أيام عمل');
        }

        return back()->with('error', '❌ ' . ($result['error'] ?? 'حدث خطأ'));
    }
}
