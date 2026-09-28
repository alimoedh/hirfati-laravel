<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Request, User, Category, WalletTransaction};
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(HttpRequest $httpRequest)
    {
        $from = $httpRequest->input('from', now()->startOfMonth()->toDateString());
        $to   = $httpRequest->input('to', now()->toDateString());

        $dateRange = [$from . ' 00:00:00', $to . ' 23:59:59'];

        // ═══════════ إحصائيات الطلبات ═══════════
        $totalRequests     = Request::whereBetween('created_at', $dateRange)->count();
        $completedRequests = Request::where('status', 'completed')->whereBetween('updated_at', $dateRange)->count();
        $pendingRequests   = Request::where('status', 'pending')->whereBetween('created_at', $dateRange)->count();
        $cancelledRequests = Request::where('status', 'cancelled')->whereBetween('created_at', $dateRange)->count();
        $emergencyRequests = Request::where('is_emergency', true)->whereBetween('created_at', $dateRange)->count();
        $installmentRequests = Request::where('use_installment', true)->whereBetween('created_at', $dateRange)->count();

        // ═══════════ الإيرادات المالية ═══════════
        $totalVolume = WalletTransaction::where('type', 'escrow_in')
            ->whereBetween('created_at', $dateRange)
            ->sum('amount');

        $totalCommissions = WalletTransaction::where('type', 'commission')
            ->whereBetween('created_at', $dateRange)
            ->sum('amount');

        $totalCraftsmenEarnings = WalletTransaction::where('type', 'escrow_release')
            ->whereBetween('created_at', $dateRange)
            ->sum('amount');

        $totalWithdrawals = WalletTransaction::where('type', 'withdraw')
            ->whereBetween('created_at', $dateRange)
            ->sum('amount');

        $avgOrderValue = $completedRequests > 0 ? round($totalVolume / $completedRequests, 2) : 0;

        // ═══════════ إحصائيات المستخدمين ═══════════
        $newUsers     = User::whereBetween('created_at', $dateRange)->count();
        $newClients   = User::where('role', 'client')->whereBetween('created_at', $dateRange)->count();
        $newCraftsmen = User::where('role', 'craftsman')->whereBetween('created_at', $dateRange)->count();

        // ═══════════ إيرادات آخر 6 أشهر ═══════════
        $monthsAr = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];

        $monthlyRaw = WalletTransaction::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, 
                SUM(CASE WHEN type = 'escrow_in' THEN amount ELSE 0 END) as volume,
                SUM(CASE WHEN type = 'commission' THEN amount ELSE 0 END) as commission,
                SUM(CASE WHEN type = 'escrow_release' THEN amount ELSE 0 END) as earnings")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month_key')->orderBy('month_key')->get();

        $mrLabels = $mrVolume = $mrCommission = $mrEarnings = [];
        foreach ($monthlyRaw as $mr) {
            $m = explode('-', $mr->month_key)[1];
            $mrLabels[]     = $monthsAr[$m] ?? $mr->month_key;
            $mrVolume[]     = (float) $mr->volume;
            $mrCommission[] = (float) $mr->commission;
            $mrEarnings[]   = (float) $mr->earnings;
        }

        // ═══════════ أعلى الحرفيين ═══════════
        $topCraftsmen = User::where('role', 'craftsman')
            ->withCount(['craftsmanRequests as completed_count' => fn($q) => $q->where('status', 'completed')])
            ->orderByDesc('completed_count')
            ->limit(5)
            ->get()
            ->map(function ($c) {
                $earnings = WalletTransaction::where('craftsman_id', $c->id)
                    ->where('type', 'escrow_release')->sum('amount');
                $c->earnings = (float) $earnings;
                return $c;
            });

        // ═══════════ الطلبات حسب التصنيف ═══════════
        $byCategory = Category::withCount(['requests as completed_count' => fn($q) => $q->where('status', 'completed')])
            ->get();

        return view('admin.reports', compact(
            'from', 'to',
            'totalRequests', 'completedRequests', 'pendingRequests', 'cancelledRequests',
            'emergencyRequests', 'installmentRequests',
            'totalVolume', 'totalCommissions', 'totalCraftsmenEarnings', 'totalWithdrawals', 'avgOrderValue',
            'newUsers', 'newClients', 'newCraftsmen',
            'mrLabels', 'mrVolume', 'mrCommission', 'mrEarnings',
            'topCraftsmen', 'byCategory'
        ));
    }

    public function exportUsers()
    {
        $users = User::with('craftsmanProfile')->latest()->get();

        $filename = 'users_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($users) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM للعربية

            fputcsv($out, ['ID', 'الاسم', 'البريد', 'الهاتف', 'الدور', 'الحالة', 'موثق', 'نقاط', 'التخصص', 'تاريخ التسجيل']);

            foreach ($users as $u) {
                $roleLabel = match($u->role) {
                    'admin'     => 'إداري',
                    'craftsman' => 'حرفي',
                    'client'    => 'عميل',
                    default     => $u->role,
                };
                fputcsv($out, [
                    $u->id,
                    $u->full_name,
                    $u->email,
                    $u->phone,
                    $roleLabel,
                    $u->is_active ? 'نشط' : 'غير نشط',
                    $u->is_verified ? 'نعم' : 'لا',
                    $u->loyalty_points,
                    $u->craftsmanProfile->category->name ?? '-',
                    $u->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportRequests(HttpRequest $httpRequest)
    {
        $from = $httpRequest->input('from', now()->startOfMonth()->toDateString());
        $to   = $httpRequest->input('to', now()->toDateString());

        $requests = Request::with(['client', 'craftsman', 'category'])
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->latest()->get();

        $filename = 'requests_' . $from . '_to_' . $to . '.csv';

        return response()->streamDownload(function () use ($requests) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['ID', 'العنوان', 'العميل', 'الحرفي', 'التصنيف', 'الحالة', 'السعر', 'طوارئ', 'تقسيط', 'التاريخ']);

            $statusLabels = [
                'pending'     => 'قيد الانتظار',
                'accepted'    => 'مقبول',
                'in_progress' => 'قيد التنفيذ',
                'completed'   => 'مكتمل',
                'cancelled'   => 'ملغي',
            ];

            foreach ($requests as $r) {
                fputcsv($out, [
                    $r->id,
                    $r->title,
                    $r->client->full_name ?? '-',
                    $r->craftsman->full_name ?? '-',
                    $r->category->name ?? '-',
                    $statusLabels[$r->status] ?? $r->status,
                    $r->budget,
                    $r->is_emergency ? 'نعم' : 'لا',
                    $r->use_installment ? 'نعم' : 'لا',
                    $r->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportFinancial(HttpRequest $httpRequest)
    {
        $from = $httpRequest->input('from', now()->startOfMonth()->toDateString());
        $to   = $httpRequest->input('to', now()->toDateString());

        $transactions = WalletTransaction::with(['craftsman', 'request'])
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->latest()->get();

        $filename = 'financial_' . $from . '_to_' . $to . '.csv';

        return response()->streamDownload(function () use ($transactions) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['ID', 'التاريخ', 'الحرفي', 'الطلب', 'النوع', 'المبلغ', 'الرصيد بعد', 'الوصف']);

            foreach ($transactions as $t) {
                fputcsv($out, [
                    $t->id,
                    $t->created_at->format('Y-m-d H:i'),
                    $t->craftsman->full_name ?? '-',
                    $t->request_id ? '#' . $t->request_id : '-',
                    $t->type_label,
                    $t->amount,
                    $t->balance_after,
                    $t->description,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
