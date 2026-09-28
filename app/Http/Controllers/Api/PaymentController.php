<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as ServiceRequest;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Request $request, PayPalService $paypal)
    {
        $data = $request->validate([
            'request_id' => 'required|integer|exists:requests,id',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        $user = auth()->user();

        abort_unless(
            ServiceRequest::where('id', $data['request_id'])->where('client_id', $user->id)->exists(),
            403
        );

        $redirectUrl = $paypal->createPayment($data['request_id'], (float) $data['amount']);

        if (!$redirectUrl) {
            return response()->json(['error' => 'فشل إنشاء عملية الدفع'], 500);
        }

        return response()->json(['success' => true, 'redirect_url' => $redirectUrl]);
    }
}
