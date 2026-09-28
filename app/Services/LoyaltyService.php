<?php

namespace App\Services;

use App\Models\{User, Reward};

class LoyaltyService
{
    public static function add(int $userId, int $points, string $actionType, string $description): bool
    {
        $user = User::find($userId);
        if (!$user) return false;

        $user->increment('loyalty_points', $points);
        $user->increment('total_points_earned', $points);

        Reward::create([
            'user_id'       => $userId,
            'points_earned' => $points,
            'action_type'   => $actionType,
            'description'   => $description,
        ]);

        return true;
    }

    public static function use(int $userId, int $points, string $description): bool
    {
        $user = User::find($userId);
        if (!$user || $user->loyalty_points < $points) return false;

        $user->decrement('loyalty_points', $points);
        $user->increment('total_points_spent', $points);

        Reward::create([
            'user_id'      => $userId,
            'points_spent' => $points,
            'action_type'  => 'bonus',
            'description'  => $description,
        ]);

        return true;
    }
}
