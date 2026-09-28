<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Services\{ActivityService, NotificationService, WalletService};
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function show(int $requestId)
    {
        $request = Request::with('category')->findOrFail($requestId);
        $user = auth()->user();

        abort_unless($request->client_id === $user->id, 403);

        if ($request->status !== 'pending') {
            return redirect()->route('client.request-details', $requestId);
        }

        return view('client.checkout', compact('request'));
    }

    public function process(HttpRequest $httpRequest, int $requestId)
    {
        $request = Request::findOrFail($requestId);
        $user = auth()->user();

        abort_unless($request->client_id === $user->id, 403);
        abort_unless($request->status === 'pending', 403);

        $httpRequest->validate([
            'payment_method' => 'required|in:card,paypal,wallet',
        ]);

        $amount = (float) ($request->budget ?? 0);
        $paymentMethod = $httpRequest->input('payment_method');
        $transactionId = 'TXN-' . strtoupper(bin2hex(random_bytes(8)));

        try {
            DB::transaction(function () use ($request, $user, $amount, $paymentMethod, $transactionId) {
                $request->update(['status' => 'accepted']);

                if ($request->craftsman_id && $amount > 0) {
                    WalletService::addToEscrow($request->craftsman_id, $request->id, $amount);
                }

                ActivityService::log(
                    $user->id, 'payment',
                    "دفع مبلغ $amount ريال للطلب #{$request->id} عبر $paymentMethod — رقم العملية: $transactionId"
                );

                NotificationService::send(
                    $user->id,
                    '✅ تم تأكيد الدفع',
                    "تم دفع مبلغ " . number_format($amount) . " ريال للطلب #" . str_pad($request->id, 4, '0', STR_PAD_LEFT),
                    url("/client/request-details/{$request->id}"),
                    'success'
                );

                if ($request->craftsman_id) {
                    NotificationService::send(
                        $request->craftsman_id,
                        '💰 تم دفع الطلب',
                        "تم دفع مبلغ " . number_format($amount) . " ريال للطلب #" . str_pad($request->id, 4, '0', STR_PAD_LEFT) . " — يمكنك بدء التنفيذ",
                        url("/craftsman/dashboard"),
                        'success'
                    );
                }
            });

            return redirect()->route('client.payment-success', [
                'id' => $request->id,
                'txn' => $transactionId,
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء معالجة الدفع: ' . $e->getMessage());
        }
    }
}
