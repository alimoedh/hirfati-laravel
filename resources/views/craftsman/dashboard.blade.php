@extends('layouts.craftsman')
@section('title', 'لوحة التحكم')

@section('content')

{{-- ═══════════ Header ═══════════ --}}
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        أهلاً {{ auth()->user()->full_name }} 👋
    </h1>
    <p class="text-slate-500 text-sm mt-1">لوحة إدارة خدماتك</p>
</div>

{{-- ═══════════ Stat Cards ═══════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- طلبات جديدة (المُسندة للحرفي بحالة pending) --}}
    <div class="stat-card-colored" style="background: linear-gradient(135deg, #FBBF24 0%, #D97706 100%); box-shadow: 0 12px 40px rgba(251,191,36,0.35);">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold opacity-80 mb-1">طلبات جديدة</p>
                <p class="text-4xl font-black" x-data="counter({{ $stats['pending'] }})">
                    <span x-text="formatted"></span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
    </div>

    {{-- مكتملة --}}
    <div class="stat-card-colored" style="background: linear-gradient(135deg, #10B981 0%, #047857 100%); box-shadow: 0 12px 40px rgba(16,185,129,0.35);">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold opacity-80 mb-1">مكتملة</p>
                <p class="text-4xl font-black" x-data="counter({{ $stats['completed'] }})">
                    <span x-text="formatted"></span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    {{-- التقييم --}}
    <div class="stat-card-colored" style="background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%); box-shadow: 0 12px 40px rgba(59,130,246,0.35);">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold opacity-80 mb-1">التقييم</p>
                <p class="text-4xl font-black">
                    {{ number_format($stats['rating'], 1) }}
                    <span class="text-lg">⭐</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-xl">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>
    </div>

    {{-- الأرباح --}}
    <div class="stat-card-colored" style="background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%); box-shadow: 0 12px 40px rgba(139,92,246,0.35);">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-bold opacity-80 mb-1">الأرباح</p>
                <p class="text-2xl font-black">{{ number_format($stats['earnings']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-xl">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════ Requests Table ═══════════ --}}
<div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-clipboard-list text-gold-500"></i>
            آخر الطلبات
        </h3>
        <a href="{{ route('craftsman.requests') }}"
           class="text-gold-600 text-sm font-bold hover:gap-2 flex items-center gap-1 transition-all">
            عرض الكل <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
    </div>

    @if($requests->isEmpty())
        <div class="text-center py-12">
            <i class="fa-solid fa-inbox text-5xl text-slate-300 mb-3"></i>
            <p class="text-slate-500 font-bold">لا توجد طلبات حالياً</p>
            <p class="text-slate-400 text-xs mt-2">ستظهر هنا الطلبات المخصصة لك والطلبات العامة في تخصصك</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full" style="border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th class="text-right py-3 px-3 text-xs font-bold text-slate-400 uppercase border-b border-slate-100">العميل</th>
                        <th class="text-right py-3 px-3 text-xs font-bold text-slate-400 uppercase border-b border-slate-100">الخدمة</th>
                        <th class="text-right py-3 px-3 text-xs font-bold text-slate-400 uppercase border-b border-slate-100">التاريخ</th>
                        <th class="text-right py-3 px-3 text-xs font-bold text-slate-400 uppercase border-b border-slate-100">الحالة</th>
                        <th class="text-right py-3 px-3 text-xs font-bold text-slate-400 uppercase border-b border-slate-100">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests->take(5) as $r)
                        @php
                            $isMyRequest = $r->craftsman_id === auth()->id();
                            $isAvailable = $r->status === 'pending' && is_null($r->craftsman_id);

                            $myBid = null;
                            if ($isAvailable) {
                                $myBid = \App\Models\Bid::where('request_id', $r->id)
                                    ->where('craftsman_id', auth()->id())
                                    ->first();
                            }
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">

                            {{-- العميل --}}
                            <td class="py-3 px-3 text-sm font-bold text-slate-800">
                                {{ $r->client->full_name ?? 'غير معروف' }}
                            </td>

                            {{-- الخدمة --}}
                            <td class="py-3 px-3 text-sm text-slate-600">
                                {{ Str::limit($r->title, 30) }}
                            </td>

                            {{-- التاريخ --}}
                            <td class="py-3 px-3 text-xs text-slate-500" dir="ltr">
                                {{ $r->created_at->format('Y-m-d') }}
                            </td>

                            {{-- الحالة --}}
                            <td class="py-3 px-3">
                                @if($isAvailable)
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2 py-1 rounded-lg text-[10px] font-bold">
                                        <i class="fa-solid fa-gavel"></i> متاح
                                    </span>
                                @else
                                    {!! $r->status_badge !!}
                                @endif
                            </td>

                            {{-- الإجراءات --}}
                            <td class="py-3 px-3">
                                <div class="flex gap-1 items-center">

                                    {{-- عرض التفاصيل --}}
                                    <a href="{{ route('client.request-details', $r->id) }}"
                                       class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-all"
                                       title="عرض التفاصيل">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    {{-- مراسلة --}}
                                    @if($r->craftsman_id)
                                        <a href="{{ route('craftsman.chat', $r->id) }}"
                                           class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center hover:bg-purple-200 transition-all"
                                           title="مراسلة العميل">
                                            <i class="fa-solid fa-comments text-xs"></i>
                                        </a>
                                    @endif

                                    {{-- إذا الطلب متاح → تقديم عرض --}}
                                    @if($isAvailable)
                                        @if($myBid)
                                            @if($myBid->status === 'pending')
                                                <button type="button"
                                                        onclick="openBidModal({{ $r->id }}, {{ $myBid->id }}, {{ $myBid->amount }}, {{ $myBid->duration_days }}, '{{ addslashes($myBid->message ?? '') }}')"
                                                        class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center hover:bg-amber-200 transition-all"
                                                        title="تعديل عرضي ({{ number_format($myBid->amount) }} ريال)">
                                                    <i class="fa-solid fa-pen text-xs"></i>
                                                </button>
                                            @else
                                                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center"
                                                      title="عرضي: {{ $myBid->status_label }}">
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                </span>
                                            @endif
                                        @else
                                            <button type="button"
                                                    onclick="openBidModal({{ $r->id }})"
                                                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-gold-400 to-gold-600 text-white flex items-center justify-center hover:shadow-glow transition-all"
                                                    title="قدم عرضك">
                                                <i class="fa-solid fa-gavel text-xs"></i>
                                            </button>
                                        @endif
                                    @endif

                                    {{-- إذا طلبي → أزرار قبول/بدء/إنهاء --}}
                                    @if($isMyRequest)

                                        {{-- قبول --}}
                                        @if($r->status === 'pending')
                                            <form method="POST" action="{{ route('craftsman.action') }}" style="display:inline">
                                                @csrf
                                                <input type="hidden" name="request_id" value="{{ $r->id }}">
                                                <button type="submit" name="request_action" value="accept"
                                                        class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-200 transition-all"
                                                        title="قبول الطلب">
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- بدء التنفيذ --}}
                                        @if($r->status === 'accepted')
                                            <form method="POST" action="{{ route('craftsman.action') }}" style="display:inline">
                                                @csrf
                                                <input type="hidden" name="request_id" value="{{ $r->id }}">
                                                <button type="submit" name="request_action" value="start"
                                                        class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center hover:bg-indigo-200 transition-all"
                                                        title="بدء التنفيذ">
                                                    <i class="fa-solid fa-play text-xs"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- إنهاء --}}
                                        @if($r->status === 'in_progress')
                                            <form method="POST" action="{{ route('craftsman.action') }}" style="display:inline"
                                                  onsubmit="return confirm('هل أنت متأكد من إنهاء الطلب؟')">
                                                @csrf
                                                <input type="hidden" name="request_id" value="{{ $r->id }}">
                                                <button type="submit" name="request_action" value="complete"
                                                        class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-200 transition-all"
                                                        title="إنهاء الطلب">
                                                    <i class="fa-solid fa-flag-checkered text-xs"></i>
                                                </button>
                                            </form>
                                        @endif

                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ═══════════ Bid Modal ═══════════ --}}
<div id="bidModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur z-50 items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-black text-lg text-slate-800">
                <i class="fa-solid fa-gavel text-gold-500"></i>
                <span id="bidModalTitle">قدم عرضك</span>
            </h3>
            <button type="button" onclick="closeBidModal()"
                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-slate-600"></i>
            </button>
        </div>

        <form method="POST" id="bidForm" action="">
            @csrf
            <input type="hidden" name="_method" id="bidMethod" value="POST">

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">
                        <i class="fa-solid fa-money-bill-wave text-gold-500"></i> السعر (ريال) *
                    </label>
                    <input type="number" name="amount" id="bidAmount" required min="1" max="999999"
                           placeholder="مثال: 5000"
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">
                        <i class="fa-solid fa-clock text-gold-500"></i> مدة التنفيذ (أيام) *
                    </label>
                    <input type="number" name="duration_days" id="bidDuration" required min="1" max="365" value="1"
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">
                        <i class="fa-solid fa-comment text-gold-500"></i> رسالة للعميل (اختياري)
                    </label>
                    <textarea name="message" id="bidMessage" rows="3" maxlength="1000"
                              placeholder="اشرح كيف ستنفذ العمل..."
                              class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm resize-none"></textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeBidModal()"
                        class="flex-1 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 font-bold text-sm text-slate-700">
                    إلغاء
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-2xl text-white font-bold text-sm flex items-center justify-center gap-2"
                        style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                    <i class="fa-solid fa-paper-plane"></i> إرسال
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════ Styles ═══════════ --}}
<style>
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
</style>

@endsection

@push('scripts')
<script>
function openBidModal(requestId, bidId, amount, duration, message) {
    const form   = document.getElementById('bidForm');
    const method = document.getElementById('bidMethod');
    const title  = document.getElementById('bidModalTitle');
    const isEdit = bidId !== undefined;

    if (isEdit) {
        form.action       = `/craftsman/bids/${bidId}`;
        method.value      = 'PUT';
        title.textContent = 'تعديل عرضك';
    } else {
        form.action       = `/craftsman/bids/${requestId}`;
        method.value      = 'POST';
        title.textContent = 'قدم عرضك';
    }

    document.getElementById('bidAmount').value   = amount || '';
    document.getElementById('bidDuration').value = duration || 1;
    document.getElementById('bidMessage').value  = message || '';

    const m = document.getElementById('bidModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closeBidModal() {
    const m = document.getElementById('bidModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

// إغلاق عند الضغط خارج النافذة
document.getElementById('bidModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeBidModal();
});

// إغلاق بمفتاح Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeBidModal();
});
</script>
@endpush
