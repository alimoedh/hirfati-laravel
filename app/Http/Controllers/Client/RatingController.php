<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Services\{ReviewService, LoyaltyService, ActivityService, NotificationService};
use Illuminate\Http\Request as HttpRequest;

class RatingController extends Controller
{
    public function show(int $requestId)
    {
        $request = Request::with(['craftsman'])->findOrFail($requestId);
        $user = auth()->user();

        abort_unless($request->client_id === $user->id, 403);
        abort_unless($request->status === 'completed', 403);
        abort_if($request->review()->exists(), 403, 'تم التقييم مسبقاً');

        return view('client.rate-craftsman', compact('request'));
    }

    public function store(HttpRequest $httpRequest, int $requestId)
    {
        $request = Request::with('craftsman')->findOrFail($requestId);
        $user = auth()->user();

        abort_unless($request->client_id === $user->id, 403);
        abort_unless($request->status === 'completed', 403);
        abort_if($request->review()->exists(), 403);

        $data = $httpRequest->validate([
            'rating'             => 'required|integer|min:1|max:5',
            'quality_rating'     => 'nullable|integer|min:1|max:5',
            'punctuality_rating' => 'nullable|integer|min:1|max:5',
            'behavior_rating'    => 'nullable|integer|min:1|max:5',
            'comment'            => 'nullable|string|max:1000',
        ]);

        ReviewService::create([
            'request_id'         => $request->id,
            'client_id'          => $user->id,
            'craftsman_id'       => $request->craftsman_id,
            'rating'             => $data['rating'],
            'quality_rating'     => $data['quality_rating'] ?? $data['rating'],
            'punctuality_rating' => $data['punctuality_rating'] ?? $data['rating'],
            'behavior_rating'    => $data['behavior_rating'] ?? $data['rating'],
            'comment'            => $data['comment'] ?? null,
        ]);

        LoyaltyService::add($user->id, 5, 'review_given', 'تقييم حرفي');

        NotificationService::send(
            $request->craftsman_id,
            '⭐ تقييم جديد',
            "قام {$user->full_name} بتقييمك بـ {$data['rating']} نجوم",
            url("/craftsman/dashboard"),
            'success'
        );

        ActivityService::log($user->id, 'rate_craftsman', "تقييم الحرفي #{$request->craftsman_id} بـ {$data['rating']} نجوم");

        return redirect()->route('client.dashboard')
            ->with('success', 'تم إرسال تقييمك بنجاح! شكراً لك على مشاركة رأيك.');
    }
}
