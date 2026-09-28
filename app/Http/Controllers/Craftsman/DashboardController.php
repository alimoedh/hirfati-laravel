<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\{Request, Installment};
use App\Services\ReviewService;
use Illuminate\Http\Request as HttpRequest;

class DashboardController extends Controller
{
       public function index()
    {
        $user = auth()->user();
        $profile = $user->craftsmanProfile;

        // الطلبات المُسندة للحرفي + الطلبات العامة في تخصصه
        $requests = Request::with(['client', 'category'])
            ->where(function ($q) use ($user, $profile) {
                // 1) طلباتي المُسندة
                $q->where('craftsman_id', $user->id);

                // 2) الطلبات العامة في تخصصي (بدون حرفي مُسند)
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

        // حالة الأقساط لكل طلب
        $installmentStatus = [];
        foreach ($requests as $r) {
            if ($r->use_installment && $r->installment_count > 0) {
                $total = Installment::where('request_id', $r->id)->count();
                $paid  = Installment::where('request_id', $r->id)->where('status', 'paid')->count();
                $installmentStatus[$r->id] = [
                    'total'    => $total,
                    'paid'     => $paid,
                    'complete' => $total > 0 && $paid >= $total,
                ];
            }
        }

        $rating = ReviewService::getCraftsmanRating($user->id);

        $stats = [
            'pending'   => $requests->where('status', 'pending')->where('craftsman_id', $user->id)->count(),
            'completed' => $requests->where('status', 'completed')->count(),
            'rating'    => $rating['avg'],
            'reviews'   => $rating['total'],
            'earnings'  => Request::where('craftsman_id', $user->id)
                ->where('status', 'completed')->sum('budget'),
        ];

        return view('craftsman.dashboard', compact('user', 'requests', 'installmentStatus', 'stats'));
    }


    public function action(HttpRequest $request)
    {
        $data = $request->validate([
            'request_id'     => 'required|integer|exists:requests,id',
            'request_action' => 'required|in:accept,reject,start,complete',
        ]);

        $user = auth()->user();
        $serviceRequest = Request::where('id', $data['request_id'])
            ->where('craftsman_id', $user->id)
            ->firstOrFail();

        try {
            match ($data['request_action']) {
                'accept'   => \App\Services\RequestService::accept($serviceRequest, $user->id),
                'reject'   => \App\Services\RequestService::reject($serviceRequest, $user->id),
                'start'    => (function () use ($serviceRequest, $user) {
                    $res = \App\Services\RequestService::start($serviceRequest, $user->id);
                    if (!$res['success']) throw new \Exception($res['error']);
                })(),
                'complete' => \App\Services\RequestService::complete($serviceRequest, $user->id),
            };

            return back()->with('success', '✅ تم تحديث حالة الطلب بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }
}
