<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Services\ReviewService;

class PortfolioController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $completedWorks = Request::with(['client'])
            ->where('craftsman_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->get();

        $rating = ReviewService::getCraftsmanRating($user->id);

        return view('craftsman.portfolio', compact('completedWorks', 'rating'));
    }
}
