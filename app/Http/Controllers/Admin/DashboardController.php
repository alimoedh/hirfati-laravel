<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Request, Complaint};

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers     = User::count();
        $totalClients   = User::clients()->count();
        $totalCraftsmen = User::craftsmen()->count();

        $pendingCraftsmen = User::with(['craftsmanProfile.category'])
            ->where('role', 'craftsman')
            ->whereHas('craftsmanProfile', fn($q) => $q->where('is_approved', 0))
            ->latest()
            ->get();

        $totalRequests     = Request::count();
        $pendingRequests   = Request::where('status', 'pending')->count();
        $completedRequests = Request::where('status', 'completed')->count();
        $totalComplaints   = Complaint::where('status', 'pending')->count();

        $monthsAr = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];

        $monthlyRaw = Request::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month_key')->orderBy('month_key')->get();

        $mrLabels = $mrData = [];
        foreach ($monthlyRaw as $mr) {
            $m = explode('-', $mr->month_key)[1];
            $mrLabels[] = $monthsAr[$m] ?? $mr->month_key;
            $mrData[]   = (int) $mr->count;
        }

        $statusRaw = Request::selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
        $stNames = ['pending'=>'قيد الانتظار','accepted'=>'مقبول','in_progress'=>'جاري التنفيذ','completed'=>'مكتمل','cancelled'=>'ملغي'];
        $stColorsMap = ['pending'=>'#F59E0B','accepted'=>'#3B82F6','in_progress'=>'#8B5CF6','completed'=>'#10B981','cancelled'=>'#EF4444'];

        $stLabels = $stCounts = $stColors = [];
        foreach ($statusRaw as $st) {
            $stLabels[] = $stNames[$st->status] ?? $st->status;
            $stCounts[] = (int) $st->count;
            $stColors[] = $stColorsMap[$st->status] ?? '#CBD5E1';
        }

        $growthRaw = User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(CASE WHEN role='client' THEN 1 ELSE 0 END) as clients, SUM(CASE WHEN role='craftsman' THEN 1 ELSE 0 END) as craftsmen")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month_key')->orderBy('month_key')->get();

        $ugLabels = $ugClients = $ugCraftsmen = [];
        foreach ($growthRaw as $ug) {
            $m = explode('-', $ug->month_key)[1];
            $ugLabels[]    = $monthsAr[$m] ?? $ug->month_key;
            $ugClients[]   = (int) $ug->clients;
            $ugCraftsmen[] = (int) $ug->craftsmen;
        }

        $recentComplaints = Complaint::with('client')->where('status', 'pending')->latest()->limit(3)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalClients', 'totalCraftsmen', 'pendingCraftsmen',
            'totalRequests', 'pendingRequests', 'completedRequests', 'totalComplaints',
            'recentComplaints', 'mrLabels', 'mrData',
            'stLabels', 'stCounts', 'stColors',
            'ugLabels', 'ugClients', 'ugCraftsmen'
        ));
    }
}
