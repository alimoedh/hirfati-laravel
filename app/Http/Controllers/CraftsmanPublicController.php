<?php

namespace App\Http\Controllers;

use App\Models\{User, Review, Request};

class CraftsmanPublicController extends Controller
{
    public function show($id)   // ✅ بدون int
    {
        // استخراج الرقم من النص (مثل: 2-علي-السباك → 2)
        $craftsmanId = (int) $id;

        if ($craftsmanId === 0) {
            abort(404);
        }

        $craftsman = User::with([
            'craftsmanProfile.category',
            'reviewsReceived.client',
        ])
        ->where('role', 'craftsman')
        ->where('is_active', true)
        ->whereHas('craftsmanProfile', fn($q) => $q->where('is_approved', true))
        ->findOrFail($craftsmanId);

        // التقييمات (آخر 10)
        $reviews = Review::with(['client', 'request'])
            ->where('craftsman_id', $craftsman->id)
            ->latest()
            ->limit(10)
            ->get();

        // إحصائيات
        $stats = [
            'total_reviews'    => $craftsman->craftsmanProfile->total_reviews ?? 0,
            'rating_avg'       => $craftsman->craftsmanProfile->rating_avg ?? 0,
            'completed_jobs'   => Request::where('craftsman_id', $craftsman->id)
                ->where('status', 'completed')->count(),
            'experience_years' => $craftsman->craftsmanProfile->experience_years ?? 0,
        ];

        // توزيع التقييمات
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($reviews as $r) {
            $distribution[(int) $r->rating]++;
        }

        // معرض الأعمال (آخر 6)
        $portfolio = Request::with('category')
            ->where('craftsman_id', $craftsman->id)
            ->where('status', 'completed')
            ->whereNotNull('problem_image')
            ->latest()
            ->limit(6)
            ->get();

        // هل هو متوفر الآن؟
        $isAvailable = $craftsman->craftsmanProfile->is_available ?? false;

        return view('public.craftsman-profile', compact(
            'craftsman',
            'reviews',
            'stats',
            'distribution',
            'portfolio',
            'isAvailable'
        ));
    }
}
