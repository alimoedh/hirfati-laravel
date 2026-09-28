<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\{Request, Installment};

class RequestController extends Controller
{
        public function index()
    {
        $user = auth()->user();
        $profile = $user->craftsmanProfile;

        $requests = Request::with(['client', 'category'])
            ->where(function ($q) use ($user, $profile) {
                // 1) طلباتي المُسندة
                $q->where('craftsman_id', $user->id);

                // 2) الطلبات العامة في تخصصي
                if ($profile) {
                    $q->orWhere(function ($q2) use ($profile) {
                        $q2->whereNull('craftsman_id')
                           ->where('category_id', $profile->category_id)
                           ->where('status', 'pending');
                    });
                }
            })
            ->orderByDesc('id')
            ->get();

        $installmentStatus = [];
        foreach ($requests as $r) {
            if ($r->use_installment && $r->installment_count > 0) {
                $total = Installment::where('request_id', $r->id)->count();
                $paid  = Installment::where('request_id', $r->id)->where('status', 'paid')->count();
                $installmentStatus[$r->id] = compact('total', 'paid');
            }
        }

        return view('craftsman.requests', compact('requests', 'installmentStatus'));
    }

}
