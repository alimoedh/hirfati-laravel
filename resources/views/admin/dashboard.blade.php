@extends('layouts.admin')
@section('title', 'لوحة التحكم')

@push('styles')
<style>
    /* ============================================
       Color-Coded Stat Cards
       ============================================ */
    .stat-card-colored {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 1.5rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        color: white;
        min-height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stat-card-colored::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        filter: blur(20px);
    }

    .stat-card-colored:hover {
        transform: translateY(-6px) scale(1.02);
    }

    .stat-card-colored.blue {
        background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.4);
    }

    .stat-card-colored.yellow {
        background: linear-gradient(135deg, #FBBF24 0%, #D97706 100%);
        box-shadow: 0 12px 40px rgba(251, 191, 36, 0.4);
    }

    .stat-card-colored.green {
        background: linear-gradient(135deg, #10B981 0%, #047857 100%);
        box-shadow: 0 12px 40px rgba(16, 185, 129, 0.4);
    }

    .stat-card-colored.red {
        background: linear-gradient(135deg, #EF4444 0%, #991B1B 100%);
        box-shadow: 0 12px 40px rgba(239, 68, 68, 0.4);
    }

    .stat-card-colored .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* ============================================
       Chart Cards
       ============================================ */
    .chart-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 4px 24px rgba(15, 30, 60, 0.06);
        transition: all 0.3s;
    }

    .chart-card:hover {
        box-shadow: 0 12px 32px rgba(15, 30, 60, 0.1);
    }

    .chart-card h3 {
        font-size: 1rem;
        font-weight: 800;
        color: #0F172A;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-card canvas {
        max-height: 260px;
    }

    /* ============================================
       Quick Actions
       ============================================ */
    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem;
        border-radius: 20px;
        background: #F8FAFC;
        transition: all 0.3s;
        cursor: pointer;
        text-decoration: none;
    }

    .quick-action:hover {
        background: #FEF3C7;
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(212, 162, 76, 0.2);
    }

    .quick-action .icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: all 0.3s;
    }

    .quick-action:hover .icon-wrap {
        transform: scale(1.1) rotate(-5deg);
    }

    /* ============================================
       Activity Feed
       ============================================ */
    .activity-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.85rem;
        border-radius: 16px;
        transition: all 0.2s;
    }

    .activity-item:hover {
        background: #F8FAFC;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')

{{-- ============================================
     Page Header
     ============================================ --}}
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        أهلاً {{ auth()->user()->full_name }} 👋
    </h1>
    <p class="text-slate-500 text-sm mt-1">
        نظرة عامة على أداء المنصة
    </p>
</div>

{{-- ============================================
     4 Stat Cards Colored
     ============================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-6">

    {{-- إجمالي المستخدمين (Blue) --}}
    <div class="stat-card-colored blue">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold opacity-80 mb-1">إجمالي المستخدمين</p>
                <p class="text-4xl font-black" x-data="counter({{ $totalUsers }})">
                    <span x-text="formatted"></span>
                </p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs relative z-10 mt-2">
            <span class="opacity-80">{{ $totalClients }} عميل · {{ $totalCraftsmen }} حرفي</span>
            <i class="fa-solid fa-arrow-trend-up opacity-60"></i>
        </div>
    </div>

    {{-- الحرفيون قيد الانتظار (Yellow) --}}
    <div class="stat-card-colored yellow">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold opacity-80 mb-1">الحرفيون قيد الانتظار</p>
                <p class="text-4xl font-black" x-data="counter({{ count($pendingCraftsmen) }})">
                    <span x-text="formatted"></span>
                </p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-user-clock"></i>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs relative z-10 mt-2">
            <span class="opacity-80">بحاجة لمراجعة</span>
        </div>
    </div>

    {{-- إجمالي الطلبات (Green) --}}
    <div class="stat-card-colored green">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold opacity-80 mb-1">إجمالي الطلبات</p>
                <p class="text-4xl font-black" x-data="counter({{ $totalRequests }})">
                    <span x-text="formatted"></span>
                </p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs relative z-10 mt-2">
            <span class="opacity-80">{{ $pendingRequests }} معلق · {{ $completedRequests }} مكتمل</span>
        </div>
    </div>

    {{-- الشكاوى قيد الانتظار (Red) --}}
    <div class="stat-card-colored red">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold opacity-80 mb-1">الشكاوى قيد الانتظار</p>
                <p class="text-4xl font-black" x-data="counter({{ $totalComplaints }})">
                    <span x-text="formatted"></span>
                </p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs relative z-10 mt-2">
            <span class="opacity-80">بحاجة لمراجعة</span>
        </div>
    </div>
</div>

{{-- ============================================
     Charts Row
     ============================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

    {{-- Monthly Requests Chart --}}
    <div class="chart-card">
        <div class="flex items-center justify-between mb-4">
            <h3>
                <i class="fa-solid fa-chart-bar text-gold-500"></i>
                الطلبات لآخر 6 أشهر
            </h3>
            <span class="text-xs text-slate-400 font-bold">الطلبات الشهرية</span>
        </div>
        @if(!empty($mrLabels))
            <canvas id="monthlyChart"></canvas>
        @else
            <div class="h-64 flex items-center justify-center text-slate-400 text-sm">
                <i class="fa-solid fa-chart-bar text-3xl opacity-30 ml-2"></i>
                لا توجد بيانات بعد
            </div>
        @endif
    </div>

    {{-- User Growth Chart --}}
    <div class="chart-card">
        <div class="flex items-center justify-between mb-4">
            <h3>
                <i class="fa-solid fa-chart-line text-gold-500"></i>
                نمو المستخدمين
            </h3>
            <span class="text-xs text-slate-400 font-bold">عملاء مقابل حرفيين</span>
        </div>
        @if(!empty($ugLabels))
            <canvas id="growthChart"></canvas>
        @else
            <div class="h-64 flex items-center justify-center text-slate-400 text-sm">
                <i class="fa-solid fa-chart-line text-3xl opacity-30 ml-2"></i>
                لا توجد بيانات بعد
            </div>
        @endif
    </div>
</div>

{{-- ============================================
     Bottom Row: 3 columns
     ============================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Status Distribution Donut --}}
    <div class="chart-card">
        <div class="flex items-center justify-between mb-4">
            <h3>
                <i class="fa-solid fa-chart-pie text-gold-500"></i>
                توزيع حالة الطلب
            </h3>
        </div>
        @if(!empty($stLabels))
            <canvas id="statusChart"></canvas>
            <div class="flex flex-wrap gap-3 mt-4 justify-center text-xs">
                @foreach($stLabels as $i => $label)
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $stColors[$i] ?? '#CBD5E1' }}"></span>
                        <span class="text-slate-600 font-semibold">{{ $label }}</span>
                        <span class="text-slate-400">({{ $stCounts[$i] }})</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="h-48 flex items-center justify-center text-slate-400 text-sm">
                <i class="fa-solid fa-chart-pie text-3xl opacity-30 ml-2"></i>
                لا توجد بيانات بعد
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="chart-card">
        <h3 class="mb-4">
            <i class="fa-solid fa-bolt text-gold-500"></i>
            إجراءات سريعة
        </h3>
        <div class="grid grid-cols-2 gap-3">

            <a href="{{ route('admin.craftsmen.index') }}" class="quick-action">
                <div class="icon-wrap bg-amber-100 text-amber-600">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">توثيق حرفي</span>
            </a>

            <a href="{{ route('admin.complaints.index') }}" class="quick-action">
                <div class="icon-wrap bg-red-100 text-red-600">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">مراجعة شكوى</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="quick-action">
                <div class="icon-wrap bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">تعديل إعدادات</span>
            </a>
            <a href="{{ route('admin.reports.export-requests') }}" class="quick-action">
                <div class="icon-wrap bg-emerald-100 text-emerald-600">
                    <i class="fa-solid fa-file-csv"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">تصدير الطلبات</span>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="quick-action">
                <div class="icon-wrap bg-purple-100 text-purple-600">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">التقارير المالية</span>
            </a>

        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="chart-card">
        <div class="flex items-center justify-between mb-4">
            <h3>
                <i class="fa-solid fa-clock-rotate-left text-gold-500"></i>
                آخر الأنشطة
            </h3>
        </div>

        <div class="space-y-1">

            {{-- آخر طلب توثيق --}}
            @if($pendingCraftsmen->isNotEmpty())
                @php $c = $pendingCraftsmen->first(); @endphp
                <a href="{{ route('admin.craftsmen.index') }}" class="activity-item">
                    <div class="activity-icon bg-amber-100 text-amber-600">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">
                            طلب توثيق جديد
                        </p>
                        <p class="text-xs text-slate-500 truncate">
                            {{ $c->full_name }} — {{ $c->craftsmanProfile->category->name ?? 'غير محدد' }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $c->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @endif

            {{-- آخر شكوى --}}
            @if($recentComplaints->isNotEmpty())
                @php $comp = $recentComplaints->first(); @endphp
                <a href="{{ route('admin.complaints.index') }}" class="activity-item">
                    <div class="activity-icon bg-red-100 text-red-600">
                        <i class="fa-solid fa-flag"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">
                            شكوى جديدة
                        </p>
                        <p class="text-xs text-slate-500 truncate">
                            السبب: {{ $comp->reason_label }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $comp->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @endif

            {{-- آخر مستخدم --}}
            @php
                $latestUser = \App\Models\User::latest()->first();
            @endphp
            @if($latestUser)
                <a href="{{ route('admin.users.index') }}" class="activity-item">
                    <div class="activity-icon bg-blue-100 text-blue-600">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">
                            مستخدم جديد
                        </p>
                        <p class="text-xs text-slate-500 truncate">
                            {{ $latestUser->full_name }} — {{ $latestUser->role === 'client' ? 'عميل' : ($latestUser->role === 'craftsman' ? 'حرفي' : 'إداري') }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $latestUser->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @endif

            {{-- آخر طلب --}}
            @php
                $latestRequest = \App\Models\Request::latest()->first();
            @endphp
            @if($latestRequest)
                <a href="{{ route('client.request-details', $latestRequest->id) }}" class="activity-item">
                    <div class="activity-icon bg-emerald-100 text-emerald-600">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">
                            طلب خدمة جديد
                        </p>
                        <p class="text-xs text-slate-500 truncate">
                            {{ $latestRequest->title }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $latestRequest->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @endif

            @if($pendingCraftsmen->isEmpty() && $recentComplaints->isEmpty() && !$latestUser && !$latestRequest)
                <div class="text-center py-8 text-slate-400 text-sm">
                    <i class="fa-solid fa-inbox text-3xl opacity-30 block mb-2"></i>
                    لا توجد أنشطة
                </div>
            @endif

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ============================================
    // Chart Defaults
    // ============================================
    Chart.defaults.font.family = "'Cairo', sans-serif";
    Chart.defaults.font.weight = '600';
    Chart.defaults.color = '#64748B';

    const colors = {
        gold: '#D4A24C',
        goldLight: 'rgba(212, 162, 76, 0.15)',
        navy: '#0F1E3C',
        navyLight: 'rgba(15, 30, 60, 0.1)',
        blue: '#3B82F6',
        blueLight: 'rgba(59, 130, 246, 0.15)',
        green: '#10B981',
        greenLight: 'rgba(16, 185, 129, 0.15)',
        yellow: '#F59E0B',
        red: '#EF4444',
        purple: '#8B5CF6',
    };

    // ============================================
    // 1) Monthly Requests Bar Chart
    // ============================================
    @if(!empty($mrLabels))
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: @json($mrLabels),
                    datasets: [{
                        label: 'عدد الطلبات',
                        data: @json($mrData),
                        backgroundColor: [
                            colors.navy,
                            colors.gold,
                            colors.navy,
                            colors.gold,
                            colors.navy,
                            colors.gold,
                        ],
                        borderRadius: 12,
                        barThickness: 32,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: colors.navy,
                            padding: 12,
                            cornerRadius: 12,
                            titleFont: { size: 13, weight: '800' },
                            bodyFont: { size: 12 },
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 11 } },
                            grid: { color: '#F1F5F9', drawBorder: false },
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } },
                        }
                    }
                }
            });
        }
    @endif

    // ============================================
    // 2) User Growth Line Chart
    // ============================================
    @if(!empty($ugLabels))
        const growthCtx = document.getElementById('growthChart');
        if (growthCtx) {
            new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: @json($ugLabels),
                    datasets: [
                        {
                            label: 'عملاء',
                            data: @json($ugClients),
                            borderColor: colors.blue,
                            backgroundColor: colors.blueLight,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointBackgroundColor: colors.blue,
                            pointBorderColor: '#FFF',
                            pointBorderWidth: 2,
                        },
                        {
                            label: 'حرفيين',
                            data: @json($ugCraftsmen),
                            borderColor: colors.gold,
                            backgroundColor: colors.goldLight,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointBackgroundColor: colors.gold,
                            pointBorderColor: '#FFF',
                            pointBorderWidth: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                padding: 16,
                                font: { size: 12, weight: '700' },
                            }
                        },
                        tooltip: {
                            backgroundColor: colors.navy,
                            padding: 12,
                            cornerRadius: 12,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 11 } },
                            grid: { color: '#F1F5F9', drawBorder: false },
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } },
                        }
                    }
                }
            });
        }
    @endif

    // ============================================
    // 3) Status Distribution Donut
    // ============================================
    @if(!empty($stLabels))
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($stLabels),
                    datasets: [{
                        data: @json($stCounts),
                        backgroundColor: @json($stColors),
                        borderWidth: 4,
                        borderColor: '#FFFFFF',
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: colors.navy,
                            padding: 12,
                            cornerRadius: 12,
                        }
                    }
                }
            });
        }
    @endif
});
</script>
@endpush
