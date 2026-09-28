<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Request, Review};

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        $query = Request::where('client_id', $user->id);
        if ($role === 'craftsman') $query = Request::where('craftsman_id', $user->id);

        $stats = [
            'total'       => (clone $query)->count(),
            'completed'   => (clone $query)->where('status', 'completed')->count(),
            'pending'     => (clone $query)->where('status', 'pending')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'cancelled'   => (clone $query)->where('status', 'cancelled')->count(),
        ];

        $monthsAr = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];

        $monthlyRaw = (clone $query)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month_key')->orderBy('month_key')->get();

        $mLabels = $mCounts = [];
        foreach ($monthlyRaw as $row) {
            $m = explode('-', $row->month_key)[1];
            $mLabels[] = $monthsAr[$m] ?? $row->month_key;
            $mCounts[] = (int) $row->count;
        }

        $statusData = [$stats['completed'], $stats['pending'], $stats['in_progress'], $stats['cancelled']];

        $reviewsStats = ['avg' => 0, 'total' => 0];
        if ($role === 'craftsman') {
            $r = Review::where('craftsman_id', $user->id)->selectRaw('AVG(rating) as avg, COUNT(*) as c')->first();
            $reviewsStats = ['avg' => round($r->avg ?? 0, 1), 'total' => (int) $r->c];
        }

        return view('client.analytics', compact('stats', 'mLabels', 'mCounts', 'statusData', 'reviewsStats'));
    }
}
