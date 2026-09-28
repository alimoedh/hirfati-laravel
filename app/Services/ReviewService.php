<?php

namespace App\Services;

use App\Models\{Review, CraftsmanProfile};

class ReviewService
{
    public static function create(array $data): Review
    {
        $review = Review::create([
            'request_id'         => $data['request_id'],
            'client_id'          => $data['client_id'],
            'craftsman_id'       => $data['craftsman_id'],
            'rating'             => $data['rating'],
            'quality_rating'     => $data['quality_rating'] ?? $data['rating'],
            'punctuality_rating' => $data['punctuality_rating'] ?? $data['rating'],
            'behavior_rating'    => $data['behavior_rating'] ?? $data['rating'],
            'comment'            => $data['comment'] ?? null,
        ]);

        $stats = Review::where('craftsman_id', $data['craftsman_id'])
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        if ($stats && $stats->total > 0) {
            CraftsmanProfile::where('user_id', $data['craftsman_id'])->update([
                'rating_avg'    => round($stats->avg_rating, 2),
                'total_reviews' => $stats->total,
            ]);
        }

        return $review;
    }

    public static function getCraftsmanRating(int $craftsmanId): array
    {
        $profile = CraftsmanProfile::where('user_id', $craftsmanId)->first();
        return [
            'avg'   => (float) ($profile->rating_avg ?? 0),
            'total' => (int)   ($profile->total_reviews ?? 0),
        ];
    }
}
