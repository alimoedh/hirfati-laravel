<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\Review;

class RatingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $reviews = Review::with(['client', 'request'])
            ->where('craftsman_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $total = $reviews->count();
        $avgRating = $total > 0 ? $reviews->sum('rating') / $total : 0;

        $avgQuality     = $reviews->where('quality_rating', '>', 0)->avg('quality_rating') ?? 0;
        $avgPunctuality = $reviews->where('punctuality_rating', '>', 0)->avg('punctuality_rating') ?? 0;
        $avgBehavior    = $reviews->where('behavior_rating', '>', 0)->avg('behavior_rating') ?? 0;

        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($reviews as $r) $distribution[(int) $r->rating]++;

        return view('craftsman.ratings', compact(
            'reviews', 'total', 'avgRating',
            'avgQuality', 'avgPunctuality', 'avgBehavior', 'distribution'
        ));
    }
}
