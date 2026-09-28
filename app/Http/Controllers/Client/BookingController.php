<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Request, User};
use Illuminate\Http\Request as HttpRequest;

class BookingController extends Controller
{
    public function index(HttpRequest $request, int $craftsmanId)
    {
        $date = $request->input('date', now()->toDateString());

        $bookedSlots = Request::where('craftsman_id', $craftsmanId)
            ->whereIn('status', ['accepted', 'in_progress'])
            ->whereDate('preferred_date', '>=', now()->toDateString())
            ->get(['preferred_date', 'preferred_time'])
            ->map(fn($r) => [
                'date' => $r->preferred_date->format('Y-m-d'),
                'time' => $r->preferred_time,
            ])->toArray();

        $availableTimes = [];
        for ($i = 8; $i <= 20; $i++) {
            $time = sprintf('%02d:00', $i);
            $booked = collect($bookedSlots)->first(
                fn($s) => $s['date'] === $date && $s['time'] === $time
            );
            if (!$booked) $availableTimes[] = $time;
        }

        $craftsman = User::findOrFail($craftsmanId);

        return view('client.booking', compact('craftsmanId', 'date', 'availableTimes', 'craftsman'));
    }
}
