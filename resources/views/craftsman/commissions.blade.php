@extends('layouts.craftsman')
@section('title', 'المحفظة')

@push('styles')
<style>
    /* ============================================
       Dark Wrapper
       ============================================ */
    .wallet-wrapper {
        background: linear-gradient(135deg, #0A1428 0%, #0F1E3C 50%, #152C4A 100%);
        border-radius: 32px;
        padding: 2rem;
        min-height: calc(100vh - 200px);
        position: relative;
        overflow: hidden;
    }

    .wallet-wrapper::before {
        content: '';
        position: absolute;
        top: -200px;
        right: -200px;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(212, 162, 76, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* ============================================
       Balance Hero Card
       ============================================ */
    .balance-hero {
        background: linear-gradient(135deg, rgba(15, 30, 60, 0.95) 0%, rgba(30, 58, 95, 0.85) 100%);
        border: 1.5px solid rgba(212, 162, 76, 0.3);
        border-radius: 32px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4), 0 0 60px rgba(212, 162, 76, 0.15);
    }

    .balance-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(212, 162, 76, 0.25) 0%, transparent 60%);
        border-radius: 50%;
        filter: blur(40px);
        pointer-events: none;
    }

    .balance-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(212, 162, 76, 0.08) 1px, transparent 1px),
            radial-gradient(circle at 70% 60%, rgba(212, 162, 76, 0.08) 1px, transparent 1px),
            radial-gradient(circle at 40% 80%, rgba(212, 162, 76, 0.08) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
    }

    .balance-title {
        color: rgba(255, 255, 255, 0.55);
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .balance-amount {
        font-size: 3rem;
        font-weight: 900;
        color: #E5B968;
        text-shadow: 0 0 30px rgba(212, 162, 76, 0.4);
        line-height: 1;
        font-family: 'Cairo', sans-serif;
    }

    .balance-currency {
        font-size: 1.2rem;
        color: rgba(229, 185, 104, 0.7);
        font-weight: 700;
        margin-right: 0.5rem;
    }

    /* ============================================
       Escrow Card
       ============================================ */
    .escrow-card {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12) 0%, rgba(59, 130, 246, 0.04) 100%);
        border: 1.5px solid rgba(59, 130, 246, 0.25);
        border-radius: 24px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .escrow-card::before {
        content: '';
        position: absolute;
        top: -30%;
        left: -20%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 60%);
        border-radius: 50%;
        filter: blur(30px);
        pointer-events: none;
    }

    .escrow-label {
        color: rgba(147, 197, 253, 0.85);
        font-size: 0.75rem;
        font-weight: 700;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .escrow-amount {
        font-size: 1.6rem;
        font-weight: 900;
        color: #93C5FD;
        line-height: 1;
    }

    /* ============================================
       Progress Bar
       ============================================ */
    .progress-gold {
        height: 8px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }

    .progress-gold-fill {
        height: 100%;
        background: linear-gradient(90deg, #D4A24C, #E5B968);
        border-radius: 12px;
        transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        box-shadow: 0 0 20px rgba(212, 162, 76, 0.6);
    }

    .progress-gold-fill::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        background: white;
        border: 3px solid #D4A24C;
        border-radius: 50%;
        box-shadow: 0 0 12px rgba(212, 162, 76, 0.8);
    }

    /* ============================================
       Action Buttons
       ============================================ */
    .btn-wallet {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 1.5rem;
        border-radius: 16px;
        font-size: 0.9rem;
        font-weight: 800;
        transition: all 0.3s;
        text-decoration: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        border: none;
    }

    .btn-wallet.gold-outline {
        background: transparent;
        border: 2px solid #D4A24C;
        color: #E5B968;
    }

    .btn-wallet.gold-outline:hover {
        background: rgba(212, 162, 76, 0.15);
        box-shadow: 0 0 30px rgba(212, 162, 76, 0.3);
        transform: translateY(-2px);
    }

    .btn-wallet.gold {
        background: linear-gradient(135deg, #D4A24C, #B8873A);
        color: white;
    }

    .btn-wallet.gold:hover {
        background: linear-gradient(135deg, #E5B968, #D4A24C);
        box-shadow: 0 8px 24px rgba(212, 162, 76, 0.5);
        transform: translateY(-2px);
    }

    /* ============================================
       Chart Container
       ============================================ */
    .chart-dark {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.02) 100%);
        border: 1.5px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        padding: 1.5rem;
    }

    /* ============================================
       Transaction Item
       ============================================ */
    .transaction-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 18px;
        transition: all 0.3s;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .transaction-item:last-child {
        border-bottom: none;
    }

    .transaction-item:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .transaction-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        position: relative;
    }

    .transaction-icon.incoming {
        background: rgba(16, 185, 129, 0.15);
        color: #34D399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .transaction-icon.outgoing {
        background: rgba(239, 68, 68, 0.15);
        color: #F87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .transaction-icon.escrow {
        background: rgba(139, 92, 246, 0.15);
        color: #A78BFA;
        border: 1px solid rgba(139, 92, 246, 0.3);
    }

    .transaction-amount {
        font-size: 1.05rem;
        font-weight: 900;
        font-family: 'Cairo', sans-serif;
    }

    .transaction-amount.plus {
        color: #34D399;
    }

    .transaction-amount.minus {
        color: #F87171;
    }

    /* ============================================
       Withdraw Form
       ============================================ */
    .withdraw-form {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.02) 100%);
        border: 1.5px solid rgba(212, 162, 76, 0.2);
        border-radius: 24px;
        padding: 1.5rem;
    }

    .input-dark {
        width: 100%;
        padding: 0.85rem 1.25rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        color: white;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
        outline: none;
        transition: all 0.3s;
    }

    .input-dark:focus {
        border-color: rgba(212, 162, 76, 0.5);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 0 4px rgba(212, 162, 76, 0.1);
    }

    .input-dark::placeholder {
        color: rgba(255, 255, 255, 0.3);
    }

    .input-dark option {
        background: #0F1E3C;
        color: white;
    }
</style>
@endpush

@section('content')

<div class="wallet-wrapper">

    {{-- ============================================
         Page Header
         ============================================ --}}
    <div class="relative z-10 mb-6">
        <h1 class="text-3xl lg:text-4xl font-black text-white flex items-center gap-3">
            <i class="fa-solid fa-wallet text-gold-400"></i>
            المحفظة
        </h1>
        <p class="text-white/50 text-sm mt-1">
            إدارة أموالك ومسحوباتك
        </p>
    </div>

    {{-- ============================================
         Balance Hero Row
         ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6 relative z-10">

        {{-- Main Balance Card --}}
        <div class="lg:col-span-2 balance-hero">

            <div class="relative z-10">
                <p class="balance-title">الرصيد المتاح</p>
                <div class="flex items-baseline">
                    <span class="balance-amount" x-data="counter({{ (int) $wallet['balance'] }})">
                        <span x-text="formatted"></span>
                    </span>
                    <span class="balance-currency">ر.ي</span>
                </div>

                {{-- Progress --}}
                <div class="mt-6" x-data="progressBar({{ $wallet['total_earned'] > 0 ? min(100, round(($wallet['total_withdrawn'] / max($wallet['total_earned'], 1)) * 100)) : 0 }})">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white/60 text-xs font-bold">
                            <span x-text="value"></span>% من هذا الشهر
                        </span>
                        <span class="text-white/40 text-xs">
                            الحد الأدنى: {{ number_format($minWithdrawal) }} ر.ي
                        </span>
                    </div>
                    <div class="progress-gold">
                        <div class="progress-gold-fill" :style="`width: ${value}%`"></div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <a href="#withdraw" class="btn-wallet gold-outline">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                        طلب سحب
                    </a>
                    <a href="{{ route('craftsman.analytics') }}" class="btn-wallet gold-outline">
    <i class="fa-solid fa-chart-line"></i>
    التحليلات
</a>

                </div>
            </div>
        </div>

        {{-- Escrow Card --}}
        <div class="escrow-card flex flex-col justify-center">
            <div class="relative z-10">
                <div class="escrow-label">
                    <i class="fa-solid fa-lock"></i>
                    في الأمانة
                </div>
                <div class="flex items-baseline">
                    <span class="escrow-amount">
                        {{ number_format($wallet['escrow'], 0) }}
                    </span>
                    <span class="text-blue-300/60 font-bold mr-2 text-sm">ر.ي</span>
                </div>
                <p class="text-blue-300/50 text-xs mt-3 leading-relaxed">
                    المبالغ المحجوزة في الأمانة حتى إكمال الخدمات
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================
         Stats Grid
         ============================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 relative z-10">

        <div class="chart-dark">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-arrow-trend-up text-emerald-400"></i>
                </div>
                <p class="text-white/50 text-xs font-bold">إجمالي الأرباح</p>
            </div>
            <p class="text-xl font-black text-emerald-400">
                {{ number_format($wallet['total_earned'], 0) }}
                <span class="text-xs text-white/40">ر.ي</span>
            </p>
        </div>

        <div class="chart-dark">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-500/15 border border-red-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-percent text-red-400"></i>
                </div>
                <p class="text-white/50 text-xs font-bold">عمولة المنصة</p>
            </div>
            <p class="text-xl font-black text-red-400">
                {{ number_format($wallet['total_commission'], 0) }}
                <span class="text-xs text-white/40">ر.ي</span>
            </p>
        </div>

        <div class="chart-dark">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-amber-400"></i>
                </div>
                <p class="text-white/50 text-xs font-bold">المسحوبات</p>
            </div>
            <p class="text-xl font-black text-amber-400">
                {{ number_format($wallet['total_withdrawn'], 0) }}
                <span class="text-xs text-white/40">ر.ي</span>
            </p>
        </div>

        <div class="chart-dark">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/15 border border-blue-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-chart-pie text-blue-400"></i>
                </div>
                <p class="text-white/50 text-xs font-bold">الإجمالي</p>
            </div>
            <p class="text-xl font-black text-blue-400">
                {{ number_format($wallet['balance'] + $wallet['escrow'], 0) }}
                <span class="text-xs text-white/40">ر.ي</span>
            </p>
        </div>
    </div>

    {{-- ============================================
         Earnings Chart
         ============================================ --}}
    <div class="chart-dark mb-6 relative z-10">
        <h3 class="text-lg font-black text-white mb-5 flex items-center gap-2">
            <i class="fa-solid fa-chart-bar text-gold-400"></i>
            الأرباح الشهرية
        </h3>
        <canvas id="earningsChart" height="100"></canvas>
    </div>

    {{-- ============================================
         Withdraw Form + Transactions
         ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative z-10">

        {{-- Withdraw Form --}}
        <div class="lg:col-span-1" id="withdraw">
            <div class="withdraw-form">
                <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-transfer text-gold-400"></i>
                    طلب سحب
                </h3>

                <form method="POST" action="{{ route('craftsman.commissions.withdraw') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-white/70 text-xs font-bold mb-2">المبلغ (ريال)</label>
                        <input type="number"
                               name="amount"
                               min="{{ $minWithdrawal }}"
                               max="{{ $wallet['balance'] }}"
                               step="100"
                               placeholder="الحد الأدنى: {{ number_format($minWithdrawal) }}"
                               class="input-dark"
                               required>
                    </div>

                    <div>
                        <label class="block text-white/70 text-xs font-bold mb-2">طريقة السحب</label>
                        <select name="method" class="input-dark" required>
                            <option value="bank">🏦 حساب بنكي</option>
                            <option value="wallet">📱 محفظة إلكترونية</option>
                            <option value="cash">💵 نقداً</option>
                        </select>
                    </div>

                    @if($wallet['balance'] >= $minWithdrawal)
                        <button type="submit" class="btn-wallet gold w-full">
                            <i class="fa-solid fa-paper-plane"></i>
                            تأكيد طلب السحب
                        </button>
                    @else
                        <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-4 text-center">
                            <i class="fa-solid fa-info-circle text-amber-400 text-xl mb-2 block"></i>
                            <p class="text-amber-200 text-xs leading-relaxed">
                                الرصيد الحالي أقل من الحد الأدنى<br>
                                ({{ number_format($minWithdrawal) }} ريال)
                            </p>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Transactions List --}}
        <div class="lg:col-span-2">
            <div class="chart-dark">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-list text-gold-400"></i>
                        آخر الحركات
                    </h3>
                    <span class="text-white/40 text-xs">
                        {{ $transactions->count() }} حركة
                    </span>
                </div>

                @if($transactions->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-20 h-20 mx-auto rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-receipt text-3xl text-white/30"></i>
                        </div>
                        <p class="text-white/60 font-bold text-sm">لا توجد حركات مالية بعد</p>
                    </div>
                @else
                    <div>
                        @foreach($transactions as $tx)
                            @php
                                $isPositive = in_array($tx->type, ['escrow_release', 'refund']);
                                $isEscrow = $tx->type === 'escrow_in';
                                $iconClass = $isEscrow ? 'escrow' : ($isPositive ? 'incoming' : 'outgoing');
                                $icon = match($tx->type) {
                                    'escrow_in'      => 'fa-lock',
                                    'escrow_release' => 'fa-unlock',
                                    'commission'     => 'fa-building',
                                    'withdraw'       => 'fa-money-bill-transfer',
                                    'refund'         => 'fa-rotate-left',
                                    default          => 'fa-circle',
                                };
                            @endphp

                            <div class="transaction-item">
                                <div class="transaction-icon {{ $iconClass }}">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-bold text-sm truncate">
                                        {{ $tx->type_label }}
                                    </p>
                                    <p class="text-white/50 text-xs truncate mt-0.5">
                                        {{ $tx->description }}
                                    </p>
                                </div>

                                <div class="text-left">
                                    <p class="transaction-amount {{ $isPositive ? 'plus' : 'minus' }}">
                                        {{ $isPositive ? '+' : '-' }}{{ number_format($tx->amount, 0) }}
                                        <span class="text-xs opacity-60">ر.ي</span>
                                    </p>
                                    <p class="text-white/40 text-xs mt-0.5">
                                        {{ $tx->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    Chart.defaults.font.family = "'Cairo', sans-serif";
    Chart.defaults.font.weight = '700';
    Chart.defaults.color = '#94A3B8';

    // ============================================
    // Earnings Chart (last 6 months mock)
    // ============================================
    const earningsCtx = document.getElementById('earningsChart');
    if (earningsCtx) {
        @php
            // رسوم بيانية آخر 6 أشهر
            $months = [];
            $values = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[] = $date->translatedFormat('M');
                $values[] = \App\Models\WalletTransaction::where('craftsman_id', auth()->id())
                    ->whereIn('type', ['escrow_release', 'refund'])
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');
            }
        @endphp

        new Chart(earningsCtx, {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'الأرباح',
                    data: @json($values),
                    backgroundColor: (ctx) => {
                        const chart = ctx.chart;
                        const { ctx: c, chartArea } = chart;
                        if (!chartArea) return 'rgba(212, 162, 76, 0.3)';
                        const gradient = c.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(212, 162, 76, 0.3)');
                        gradient.addColorStop(1, 'rgba(229, 185, 104, 0.95)');
                        return gradient;
                    },
                    borderRadius: 12,
                    borderSkipped: false,
                    barThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F1E3C',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { size: 13, weight: '800' },
                        bodyFont: { size: 12 },
                        borderColor: 'rgba(212, 162, 76, 0.3)',
                        borderWidth: 1,
                        callbacks: {
                            label: (ctx) => ` ${ctx.parsed.y.toLocaleString('ar-EG')} ر.ي`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 11 },
                            color: 'rgba(255, 255, 255, 0.4)',
                            callback: (v) => v >= 1000 ? (v / 1000) + 'K' : v
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: 'rgba(255, 255, 255, 0.5)' },
                    }
                }
            }
        });
    }
});
</script>
@endpush
