<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Request, Installment};
use App\Services\{ActivityService, NotificationService, InstallmentService, WalletService};
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    public function index(HttpRequest $request)
    {
        $user = auth()->user();
        $requestId = $request->input('request_id');

        if ($requestId) {
            $installments = Installment::with('request')
                ->where('request_id', $requestId)
                ->whereHas('request', fn($q) => $q->where('client_id', $user->id))
                ->orderBy('current_installment')
                ->get();
        } else {
            $installments = Installment::with('request')
                ->whereHas('request', fn($q) => $q->where('client_id', $user->id))
                ->orderBy('due_date')
                ->get();
        }

        $stats = [
            'total'     => $installments->count(),
            'paid'      => $installments->where('status', 'paid')->count(),
            'pending'   => $installments->where('status', 'pending')->count(),
            'overdue'   => $installments->filter(fn($i) => $i->is_overdue)->count(),
            'remaining' => $installments->where('status', '!=', 'paid')
                ->sum(fn($i) => $i->per_installment_amount),
        ];

        return view('client.installments', compact('installments', 'stats', 'requestId'));
    }

    public function pay(HttpRequest $request, Installment $installment)
    {
        $user = auth()->user();
        abort_unless($installment->request->client_id === $user->id, 403);
        abort_if($installment->status === 'paid', 400, 'هذا القسط مدفوع مسبقاً');

        $amount = $installment->per_installment_amount;
        $req = $installment->request;

        DB::transaction(function () use ($installment, $req, $amount, $user) {
            InstallmentService::pay($installment);

            if ($req->craftsman_id) {
                WalletService::addToEscrow($req->craftsman_id, $req->id, $amount);
            }

            ActivityService::log($user->id, 'pay_installment', "دفع قسط للطلب #{$req->id} بمبلغ {$amount}");

            NotificationService::send(
                $user->id,
                '✅ تم دفع القسط',
                "تم دفع قسط بمبلغ " . number_format($amount) . " ريال للطلب #" . str_pad($req->id, 4, '0', STR_PAD_LEFT),
                url("/client/installments?request_id={$req->id}"),
                'success'
            );
        });

        return back()->with('success', '✅ تم دفع القسط بنجاح');
    }

    public function payAll(HttpRequest $request)
    {
        $data = $request->validate([
            'request_id' => 'required|integer|exists:requests,id',
        ]);

        $user = auth()->user();
        $req = Request::findOrFail($data['request_id']);
        abort_unless($req->client_id === $user->id, 403);

        $unpaid = Installment::where('request_id', $req->id)
            ->where('status', '!=', 'paid')
            ->orderBy('current_installment')
            ->get();

        if ($unpaid->isEmpty()) {
            return back()->with('error', 'جميع الأقساط مدفوعة');
        }

        $totalToPay = 0;
        $paidCount = 0;

        DB::transaction(function () use ($unpaid, $req, $user, &$totalToPay, &$paidCount) {
            foreach ($unpaid as $inst) {
                $amount = $inst->per_installment_amount;
                $totalToPay += $amount;

                InstallmentService::pay($inst);

                if ($req->craftsman_id) {
                    WalletService::addToEscrow($req->craftsman_id, $req->id, $amount);
                }

                $paidCount++;
            }

            ActivityService::log($user->id, 'pay_all_installments', "دفع {$paidCount} قسط للطلب #{$req->id} بمبلغ {$totalToPay}");

            NotificationService::send(
                $user->id,
                '✅ تم دفع جميع الأقساط',
                "تم دفع {$paidCount} قسط بمبلغ " . number_format($totalToPay) . " ريال",
                url("/client/installments?request_id={$req->id}"),
                'success'
            );

            if ($req->craftsman_id) {
                NotificationService::send(
                    $req->craftsman_id,
                    '💰 تم دفع جميع الأقساط',
                    "العميل أكمل دفع جميع الأقساط للطلب #" . str_pad($req->id, 4, '0', STR_PAD_LEFT) . " — يمكنك بدء التنفيذ",
                    url("/craftsman/dashboard"),
                    'success'
                );
            }
        });

        return back()->with('success', "✅ تم دفع {$paidCount} قسط بنجاح — المبلغ الكامل " . number_format($totalToPay) . " ريال");
    }
}
