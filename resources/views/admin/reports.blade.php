@extends('layouts.admin')
@section('title', 'التقارير المالية')

@section('content')

{{-- Header --}}
<div class="mb-6 flex items-center justify-between flex-wrap gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
            <i class="fa-solid fa-chart-line text-gold-500"></i> التقارير المالية
        </h1>
        <p class="text-slate-500 text-sm mt-1">نظرة شاملة على إيرادات المنصة</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100 mb-5">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[150px]">
            <label class="block text-xs font-bold text-slate-600 mb-2">من تاريخ</label>
            <input type="date" name="from" value="{{ $from }}"
                   class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
        </div>
        <div class="flex-1 min-w-[150px]">
            <label class="block text-xs font-bold text-slate-600 mb-2">إلى تاريخ</label>
            <input type="date" name="to" value="{{ $to }}"
                   class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
        </div>
        <button type="submit" class="px-6 py-2.5 rounded-xl text-white font-bold text-sm"
                style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-filter"></i> تصفية
        </button>
        <a href="{{ route('admin.reports.export-financial', ['from' => $from, 'to' => $to]) }}"
           class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm">
            <i class="fa-solid fa-file-csv"></i> تصدير CSV
        </a>
    </form>
</div>

{{-- Financial Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500">إجمالي المعاملات</span>
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-blue-600">{{ number_format($totalVolume) }}</p>
        <p class="text-xs text-slate-400 mt-1">ر.ي</p>
    </div>

    <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500">عمولة المنصة</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <i class="fa-solid fa-percent"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-emerald-600">{{ number_format($totalCommissions) }}</p>
        <p class="text-xs text-slate-400 mt-1">ر.ي</p>
    </div>

    <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500">أرباح الحرفيين</span>
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <i class="fa-solid fa-user-gear"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-amber-600">{{ number_format($totalCraftsmenEarnings) }}</p>
        <p class="text-xs text-slate-400 mt-1">ر.ي</p>
    </div>

    <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500">متوسط قيمة الطلب</span>
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-purple-600">{{ number_format($avgOrderValue) }}</p>
        <p class="text-xs text-slate-400 mt-1">ر.ي</p>
    </div>
</div>

{{-- Requests Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
    @foreach([
        ['إجمالي الطلبات', $totalRequests, 'fa-list', 'slate'],
        ['مكتملة', $completedRequests, 'fa-circle-check', 'emerald'],
        ['قيد الانتظار', $pendingRequests, 'fa-clock', 'amber'],
        ['ملغاة', $cancelledRequests, 'fa-xmark', 'red'],
        ['طوارئ', $emergencyRequests, 'fa-bolt', 'red'],
        ['تقسيط', $installmentRequests, 'fa-credit-card', 'blue'],
    ] as $s)
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-soft text-center">
            <i class="fa-solid {{ $s[2] }} text-{{ $s[3] }}-500 text-xl mb-2"></i>
            <p class="text-2xl font-black text-slate-800">{{ $s[1] }}</p>
            <p class="text-xs text-slate-500 font-bold">{{ $s[0] }}</p>
        </div>
    @endforeach
</div>

{{-- Users Stats --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-4 text-white">
        <p class="text-xs opacity-80 font-bold mb-1">مستخدمين جدد</p>
        <p class="text-3xl font-black">{{ $newUsers }}</p>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-4 text-white">
        <p class="text-xs opacity-80 font-bold mb-1">عملاء جدد</p>
        <p class="text-3xl font-black">{{ $newClients }}</p>
    </div>
    <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-4 text-white">
        <p class="text-xs opacity-80 font-bold mb-1">حرفيين جدد</p>
        <p class="text-3xl font-black">{{ $newCraftsmen }}</p>
    </div>
</div>

{{-- Chart --}}
<div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 mb-6">
    <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-chart-bar text-gold-500"></i>
        إيرادات آخر 6 أشهر
    </h3>
    <canvas id="revenueChart" height="80"></canvas>
</div>

{{-- Top Craftsmen + Categories --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
        <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-trophy text-amber-500"></i>
            أعلى 5 حرفيين
        </h3>
        @if($topCraftsmen->isEmpty())
            <p class="text-center text-slate-400 py-8">لا توجد بيانات</p>
        @else
            <div class="space-y-3">
                @foreach($topCraftsmen as $i => $c)
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white
                            @if($i === 0) bg-amber-500
                            @elseif($i === 1) bg-slate-400
                            @elseif($i === 2) bg-amber-700
                            @else bg-slate-300 @endif">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate">{{ $c->full_name }}</p>
                            <p class="text-xs text-slate-500">{{ $c->completed_count }} طلب مكتمل</p>
                        </div>
                        <div class="text-left">
                            <p class="font-black text-emerald-600 text-sm">{{ number_format($c->earnings) }}</p>
                            <p class="text-xs text-slate-400">ر.ي</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
        <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-tags text-gold-500"></i>
            الطلبات حسب التصنيف
        </h3>
        @if($byCategory->isEmpty())
            <p class="text-center text-slate-400 py-8">لا توجد بيانات</p>
        @else
            <div class="space-y-3">
                @foreach($byCategory as $cat)
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-brand-800 text-gold-400 flex items-center justify-center">
                            <i class="fa-solid {{ $cat->icon }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 text-sm">{{ $cat->name }}</p>
                        </div>
                        <span class="text-lg font-black text-slate-700">{{ $cat->completed_count }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    Chart.defaults.font.family = "'Cairo', sans-serif";
    Chart.defaults.font.weight = '700';

    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($mrLabels),
                datasets: [
                    {
                        label: 'إجمالي المعاملات',
                        data: @json($mrVolume),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderRadius: 10,
                        barThickness: 22,
                    },
                    {
                        label: 'عمولة المنصة',
                        data: @json($mrCommission),
                        backgroundColor: 'rgba(16, 185, 129, 0.9)',
                        borderRadius: 10,
                        barThickness: 22,
                    },
                    {
                        label: 'أرباح الحرفيين',
                        data: @json($mrEarnings),
                        backgroundColor: 'rgba(212, 162, 76, 0.7)',
                        borderRadius: 10,
                        barThickness: 22,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, padding: 15 } },
                    tooltip: {
                        backgroundColor: '#0F1E3C',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('ar-EG')} ر.ي`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => v >= 1000 ? (v/1000) + 'K' : v, font: { size: 11 } },
                        grid: { color: '#F1F5F9', drawBorder: false },
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
@endpush
