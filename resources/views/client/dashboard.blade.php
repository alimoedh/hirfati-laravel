@extends('layouts.client')
@section('title', 'لوحة التحكم')

@push('styles')
<style>
    /* ============================================
       Stat Card - Big Number with Sparkle
       ============================================ */
    .stat-card-big {
        background: #FFFFFF;
        border-radius: 24px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(15, 30, 60, 0.06);
        border: 1px solid #F1F5F9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 160px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stat-card-big:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(15, 30, 60, 0.12);
    }

    .stat-card-big .sparkle {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        width: 30px;
        height: 30px;
        opacity: 0.4;
    }

    .stat-card-big .big-number {
        font-size: 3.5rem;
        font-weight: 900;
        line-height: 1;
        color: #0F1E3C;
        font-family: 'Cairo', sans-serif;
    }

    /* ============================================
       Donut Card
       ============================================ */
    .donut-card {
        background: #FFFFFF;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 4px 24px rgba(15, 30, 60, 0.06);
        border: 1px solid #F1F5F9;
        min-height: 160px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ============================================
       Progress Card
       ============================================ */
    .progress-card {
        background: #FFFFFF;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 4px 24px rgba(15, 30, 60, 0.06);
        border: 1px solid #F1F5F9;
    }

    .progress-bar-track {
        height: 10px;
        background: #F1F5F9;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #D4A24C, #E5B968);
        border-radius: 12px;
        transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .progress-bar-fill::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        background: #FFFFFF;
        border: 3px solid #D4A24C;
        border-radius: 50%;
        box-shadow: 0 0 12px rgba(212, 162, 76, 0.6);
    }

    /* ============================================
       Quick Action
       ============================================ */
    .quick-action-modern {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-radius: 20px;
        background: #FFFFFF;
        border: 1.5px solid #F1F5F9;
        transition: all 0.3s;
        cursor: pointer;
        text-decoration: none;
        gap: 1rem;
    }

    .quick-action-modern:hover {
        border-color: #D4A24C;
        background: #FEF9E7;
        transform: translateX(-4px);
        box-shadow: 0 8px 24px rgba(212, 162, 76, 0.15);
    }

    .quick-action-modern.primary {
        background: linear-gradient(135deg, #D4A24C, #B8873A);
        border: none;
        color: white;
        box-shadow: 0 8px 24px rgba(212, 162, 76, 0.3);
    }

    .quick-action-modern.primary:hover {
        background: linear-gradient(135deg, #E5B968, #D4A24C);
        box-shadow: 0 12px 32px rgba(212, 162, 76, 0.5);
        transform: translateX(-4px) translateY(-2px);
    }

    .quick-action-modern .qa-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .quick-action-modern.primary .qa-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    /* ============================================
       Table Modern
       ============================================ */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern thead th {
        padding: 0.75rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: right;
        border-bottom: 1px solid #F1F5F9;
    }

    .table-modern tbody tr {
        transition: all 0.2s;
    }

    .table-modern tbody tr:hover {
        background: #F8FAFC;
    }

    .table-modern tbody td {
        padding: 1rem 0.5rem;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #F8FAFC;
        text-align: right;
    }

    /* ============================================
       Time Badge
       ============================================ */
    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .time-badge.now {
        background: #FEF3C7;
        color: #92400E;
    }

    .time-badge.later {
        background: #E0E7FF;
        color: #4338CA;
    }

    /* ============================================
       Notification Item
       ============================================ */
    .notif-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.85rem 0;
        border-bottom: 1px solid #F1F5F9;
        transition: all 0.2s;
    }

    .notif-item:last-child {
        border-bottom: none;
    }

    .notif-item:hover {
        background: #F8FAFC;
        padding-right: 0.5rem;
        border-radius: 12px;
    }

    .notif-icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
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
     Main 3-Card Row (Stat + Donut + Progress)
     ============================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Big Number Card --}}
    <div class="stat-card-big">
        <svg class="sparkle" viewBox="0 0 24 24" fill="none">
            <path d="M12 2L13.5 8.5L20 10L13.5 11.5L12 18L10.5 11.5L4 10L10.5 8.5L12 2Z" fill="#D4A24C"/>
        </svg>
        <div class="flex-1 flex items-center">
            <div class="big-number" x-data="counter({{ $stats['total'] }})">
                <span x-text="formatted"></span>
            </div>
        </div>
        <div>
            <p class="text-slate-500 text-sm font-bold">طلب نشط</p>
        </div>
    </div>

    {{-- Donut Chart Card --}}
    <div class="donut-card">
        <div class="relative">
            <canvas id="completedChart" width="160" height="160"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <p class="text-4xl font-black text-slate-800" x-data="counter({{ $stats['completed'] }})">
                    <span x-text="formatted"></span>
                </p>
                <p class="text-xs text-slate-500 font-bold mt-1">مكتمل</p>
            </div>
        </div>
    </div>

    {{-- Progress Card --}}
    <div class="progress-card flex flex-col justify-center" x-data="progressBar({{ $stats['total'] > 0 ? round(($stats['in_progress'] / max($stats['total'], 1)) * 100) : 0 }})">
        <div class="flex items-center justify-between mb-3">
            <p class="text-slate-800 font-black text-lg">
                <span class="text-3xl" x-text="{{ $stats['in_progress'] }}"></span>
                <span class="text-sm text-slate-500 font-bold mr-1">قيد التنفيذ</span>
            </p>
        </div>
        <div class="progress-bar-track">
            <div class="progress-bar-fill" :style="`width: ${value}%`"></div>
        </div>
        <p class="text-xs text-slate-400 mt-2 font-bold">
            <span x-text="value"></span>% من إجمالي الطلبات
        </p>
    </div>
</div>

{{-- ============================================
     Quick Actions Row
     ============================================ --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    {{-- New Request - Primary --}}
    <a href="{{ route('client.request-form') }}" class="quick-action-modern primary">
        <div class="flex items-center gap-3">
            <div class="qa-icon">
                <i class="fa-solid fa-plus"></i>
            </div>
            <div>
                <p class="font-black text-sm">طلب جديد</p>
                <p class="text-xs opacity-80">اطلب خدمة الآن</p>
            </div>
        </div>
        <i class="fa-solid fa-arrow-left opacity-60"></i>
    </a>

    {{-- Search --}}
    <a href="{{ route('client.search') }}" class="quick-action-modern">
        <div class="flex items-center gap-3">
            <div class="qa-icon bg-blue-100 text-blue-600">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <div>
                <p class="font-black text-sm text-slate-800">بحث</p>
                <p class="text-xs text-slate-500">ابحث عن حرفي</p>
            </div>
        </div>
        <i class="fa-solid fa-arrow-left text-slate-300"></i>
    </a>

    {{-- Messages --}}
    <a href="{{ route('client.messages') }}" class="quick-action-modern">
        <div class="flex items-center gap-3">
            <div class="qa-icon bg-emerald-100 text-emerald-600">
                <i class="fa-solid fa-envelope"></i>
            </div>
            <div>
                <p class="font-black text-sm text-slate-800">رسائل</p>
                <p class="text-xs text-slate-500">
                    @if($unreadMessages > 0)
                        {{ $unreadMessages }} غير مقروء
                    @else
                        لا جديد
                    @endif
                </p>
            </div>
        </div>
        @if($unreadMessages > 0)
            <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full">
                {{ $unreadMessages }}
            </span>
        @else
            <i class="fa-solid fa-arrow-left text-slate-300"></i>
        @endif
    </a>
</div>

{{-- ============================================
     Bottom Row: Table + Notifications
     ============================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Active Requests Table (2 cols) --}}
    <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-gold-500"></i>
                    الطلبات النشطة
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    آخر {{ $requests->take(5)->count() }} من طلباتك
                </p>
            </div>
            <a href="{{ route('client.request-form') }}" class="text-gold-600 text-sm font-bold hover:gap-2 flex items-center gap-1 transition-all">
                عرض الكل
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
        </div>

        @if($requests->isEmpty())
            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-slate-100 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-inbox text-3xl text-slate-400"></i>
                </div>
                <p class="font-bold text-slate-700 mb-1">لا توجد طلبات بعد</p>
                <p class="text-sm text-slate-500 mb-4">ابدأ بطلب خدمة جديدة الآن</p>
                <a href="{{ route('client.request-form') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 text-white font-bold text-sm">
                    <i class="fa-solid fa-plus"></i>
                    اطلب خدمة
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>عنوان الطلب</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests->take(5) as $req)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-800 to-brand-600 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-wrench text-gold-400 text-sm"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800 truncate">
                                                طلبات الطلب #{{ str_pad($req->id, 3, '0', STR_PAD_LEFT) }}
                                            </p>
                                            <p class="text-xs text-slate-500 truncate">
                                                {{ $req->title }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'pending'     => ['السالبة', 'time-badge now', 'fa-clock'],
                                            'accepted'    => ['مقبول', 'time-badge later', 'fa-check'],
                                            'in_progress' => ['ممتاز', 'time-badge later', 'fa-spinner'],
                                            'completed'   => ['مكتمل', 'time-badge later', 'fa-circle-check'],
                                            'cancelled'   => ['ملغي', 'time-badge now', 'fa-xmark'],
                                        ];
                                        $s = $statusMap[$req->status] ?? ['غير معروف', 'time-badge later', 'fa-question'];
                                    @endphp
                                    <span class="{{ $s[1] }}">
                                        <i class="fa-solid {{ $s[2] }}"></i>
                                        {{ $s[0] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs text-slate-500 font-bold" dir="ltr">
                                        {{ $req->created_at->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('client.request-details', $req->id) }}"
                                       class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-gold-100 hover:text-gold-600 text-slate-500 flex items-center justify-center transition-all">
                                        <i class="fa-solid fa-arrow-left text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-bell text-gold-500"></i>
                الإشعارات
            </h3>
            @if($notifications->count() > 0)
                <span class="bg-gold-100 text-gold-700 text-[10px] font-bold px-2 py-1 rounded-full">
                    {{ $notifications->count() }}
                </span>
            @endif
        </div>

        @if($notifications->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-bell-slash text-2xl text-slate-400"></i>
                </div>
                <p class="text-sm text-slate-500">لا توجد إشعارات حالياً</p>
            </div>
        @else
            <div>
                @foreach($notifications as $notif)
                    @php
                        $iconMap = [
                            'info'    => ['fa-info', 'bg-blue-100 text-blue-600'],
                            'success' => ['fa-check', 'bg-emerald-100 text-emerald-600'],
                            'warning' => ['fa-exclamation-triangle', 'bg-amber-100 text-amber-600'],
                            'danger'  => ['fa-exclamation', 'bg-red-100 text-red-600'],
                        ];
                        $icon = $iconMap[$notif->type] ?? $iconMap['info'];
                    @endphp
                    <div class="notif-item">
                        <div class="notif-icon-circle {{ $icon[1] }}">
                            <i class="fa-solid {{ $icon[0] }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate">
                                {{ $notif->title }}
                            </p>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                {{ $notif->message }}
                            </p>
                            <p class="text-[10px] text-slate-400 mt-1">
                                <i class="fa-regular fa-clock"></i>
                                {{ $notif->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ============================================
     Feature Badges Row
     ============================================ --}}
<div class="mt-6 flex flex-wrap gap-3">
    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold">
        <i class="fa-solid fa-robot"></i>
        تقدير ذكي
    </span>
    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold">
        <i class="fa-solid fa-shield-halved"></i>
        ضمان 30 يوم
    </span>
    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-xs font-bold">
        <i class="fa-solid fa-bolt"></i>
        طوارئ
    </span>
    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-amber-50 border border-amber-100 text-amber-700 text-xs font-bold">
        <i class="fa-solid fa-coins"></i>
        تقسيط
    </span>
    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-purple-50 border border-purple-100 text-purple-700 text-xs font-bold">
        <i class="fa-solid fa-video"></i>
        بث مباشر
    </span>
    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-fuchsia-50 border border-fuchsia-100 text-fuchsia-700 text-xs font-bold">
        <i class="fa-solid fa-cube"></i>
        واقع معزز
    </span>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    Chart.defaults.font.family = "'Cairo', sans-serif";
    Chart.defaults.font.weight = '700';

    // ============================================
    // Completed Donut Chart
    // ============================================
    const completedCtx = document.getElementById('completedChart');
    if (completedCtx) {
        const completed = {{ $stats['completed'] }};
        const total = {{ $stats['total'] }};
        const pending = Math.max(total - completed, 0);

        new Chart(completedCtx, {
            type: 'doughnut',
            data: {
                labels: ['مكتمل', 'غير مكتمل'],
                datasets: [{
                    data: [completed, pending || 0.01],
                    backgroundColor: ['#D4A24C', '#0F1E3C'],
                    borderWidth: 0,
                    cutout: '75%',
                    spacing: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1500,
                    easing: 'easeOutQuart',
                }
            }
        });
    }
});
</script>
@endpush
