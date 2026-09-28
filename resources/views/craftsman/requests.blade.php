@extends('layouts.craftsman')
@section('title', 'طلبات الخدمات')

@section('content')

<div x-data="{ active: 'all' }">

    {{-- ═══════════ Header ═══════════ --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
                <i class="fa-solid fa-list-check text-gold-500"></i>
                طلبات الخدمات
            </h1>
            <p class="text-slate-500 text-sm mt-1">{{ $requests->count() }} طلب إجمالي</p>
        </div>
    </div>

    {{-- ═══════════ Stats Row ═══════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-5">
        @php
            $stats = [
                ['my_pending',    'قيد الانتظار', 'fa-clock',          'amber'],
                ['available',     'طلبات متاحة',   'fa-gavel',          'gold'],
                ['accepted',      'مقبولة',         'fa-handshake',      'blue'],
                ['in_progress',   'قيد التنفيذ',   'fa-spinner',        'purple'],
                ['completed',     'مكتملة',         'fa-circle-check',   'emerald'],
            ];
            $counts = [
                'my_pending'  => $requests->where('status', 'pending')->where('craftsman_id', auth()->id())->count(),
                'available'   => $requests->where('status', 'pending')->whereNull('craftsman_id')->count(),
                'accepted'    => $requests->where('status', 'accepted')->where('craftsman_id', auth()->id())->count(),
                'in_progress' => $requests->where('status', 'in_progress')->where('craftsman_id', auth()->id())->count(),
                'completed'   => $requests->where('status', 'completed')->where('craftsman_id', auth()->id())->count(),
            ];
        @endphp

        @foreach($stats as $s)
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-soft">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-{{ $s[3] }}-100 text-{{ $s[3] }}-600 flex items-center justify-center">
                        <i class="fa-solid {{ $s[2] }}"></i>
                    </div>
                    <div>
                        <p class="text-xl font-black text-slate-800">{{ $counts[$s[0]] }}</p>
                        <p class="text-xs text-slate-500 font-bold">{{ $s[1] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══════════ Filter Chips ═══════════ --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <button @click="active = 'all'"
                :class="active === 'all' ? 'bg-brand-800 text-white shadow-lg' : 'bg-white text-slate-600 hover:border-brand-300'"
                class="px-5 py-2.5 rounded-2xl border-2 border-slate-200 font-bold text-sm transition-all">
            الكل
        </button>
        <button @click="active = 'available'"
                :class="active === 'available' ? 'bg-amber-500 text-white shadow-lg' : 'bg-white text-slate-600'"
                class="px-5 py-2.5 rounded-2xl border-2 border-slate-200 font-bold text-sm transition-all">
            <i class="fa-solid fa-gavel"></i> طلبات متاحة
        </button>
        <button @click="active = 'pending'"
                :class="active === 'pending' ? 'bg-amber-500 text-white shadow-lg' : 'bg-white text-slate-600'"
                class="px-5 py-2.5 rounded-2xl border-2 border-slate-200 font-bold text-sm transition-all">
            <i class="fa-solid fa-clock"></i> قيد الانتظار
        </button>
        <button @click="active = 'accepted'"
                :class="active === 'accepted' ? 'bg-blue-500 text-white shadow-lg' : 'bg-white text-slate-600'"
                class="px-5 py-2.5 rounded-2xl border-2 border-slate-200 font-bold text-sm transition-all">
            <i class="fa-solid fa-handshake"></i> مقبولة
        </button>
        <button @click="active = 'in_progress'"
                :class="active === 'in_progress' ? 'bg-purple-500 text-white shadow-lg' : 'bg-white text-slate-600'"
                class="px-5 py-2.5 rounded-2xl border-2 border-slate-200 font-bold text-sm transition-all">
            <i class="fa-solid fa-spinner"></i> قيد التنفيذ
        </button>
        <button @click="active = 'completed'"
                :class="active === 'completed' ? 'bg-emerald-500 text-white shadow-lg' : 'bg-white text-slate-600'"
                class="px-5 py-2.5 rounded-2xl border-2 border-slate-200 font-bold text-sm transition-all">
            <i class="fa-solid fa-circle-check"></i> مكتملة
        </button>
    </div>

    {{-- ═══════════ Requests Grid ═══════════ --}}
    @if($requests->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center shadow-soft border border-slate-100">
            <i class="fa-solid fa-inbox text-5xl text-slate-300 mb-4"></i>
            <p class="font-bold text-slate-700 mb-2">لا توجد طلبات</p>
            <p class="text-sm text-slate-500">سيظهر هنا الطلبات المخصصة لك والطلبات العامة في تخصصك</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($requests as $req)
                @php
                    $isMyRequest = $req->craftsman_id === auth()->id();
                    $isAvailable = $req->status === 'pending' && is_null($req->craftsman_id);
                    $filterKey   = $isAvailable ? 'available' : $req->status;

                    // هل قدمت عرضاً؟
                    $myBid = null;
                    if ($isAvailable) {
                        $myBid = \App\Models\Bid::where('request_id', $req->id)
                            ->where('craftsman_id', auth()->id())
                            ->first();
                    }

                    $bidsCount = $isAvailable ? \App\Models\Bid::where('request_id', $req->id)->where('status', 'pending')->count() : 0;

                    // عدد صور العمل
                    $beforeCount = $isMyRequest ? $req->beforePhotos()->count() : 0;
                    $afterCount  = $isMyRequest ? $req->afterPhotos()->count() : 0;
                @endphp

                <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100 hover:shadow-lift transition-all flex flex-col"
                     x-show="active === 'all' || active === '{{ $filterKey }}'"
                     x-transition>

                    {{-- Header --}}
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-800 to-brand-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid {{ $req->category->icon ?? 'fa-wrench' }} text-gold-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 text-sm leading-tight line-clamp-2">{{ $req->title }}</h3>
                            <p class="text-xs text-slate-500 mt-1">#{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        @if($isAvailable)
                            <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-lg text-[10px] font-bold flex-shrink-0">
                                🎯 متاح
                            </span>
                        @endif
                    </div>

                    {{-- Client --}}
                    <div class="flex items-center gap-2 mb-4 pb-4 border-b border-slate-100">
                        <img src="{{ $req->client->avatar_url ?? asset('assets/images/logo.png') }}"
                             class="w-8 h-8 rounded-full border-2 border-gold-400 object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $req->client->full_name ?? 'عميل' }}</p>
                            <p class="text-xs text-slate-400">{{ $req->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs text-slate-400">السعر المقترح</p>
                            <p class="text-lg font-black text-gold-600">
                                {{ number_format($req->budget ?? 0) }}
                                <span class="text-xs">ر.ي</span>
                            </p>
                        </div>
                        <div class="flex gap-1">
                            @if($req->is_emergency) <span title="طوارئ" class="text-lg">🚨</span> @endif
                            @if($req->use_installment) <span title="تقسيط" class="text-lg">💳</span> @endif
                            @if($req->has_warranty) <span title="ضمان" class="text-lg">🛡️</span> @endif
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        @if($isAvailable)
                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-xl text-xs font-bold">
                                <i class="fa-solid fa-gavel"></i>
                                طلب مفتوح للعروض ({{ $bidsCount }})
                            </span>
                        @else
                            {!! $req->status_badge !!}
                        @endif
                    </div>

                    {{-- 🆕 صور العمل (Before/After) --}}
                    @if($isMyRequest && in_array($req->status, ['in_progress', 'completed']))
                        <div class="mb-4 pt-4 border-t border-slate-100">
                            <p class="text-xs font-bold text-slate-500 mb-2 flex items-center gap-1">
                                <i class="fa-solid fa-camera-retro text-gold-500"></i>
                                توثيق العمل
                            </p>
                            <div class="flex gap-2">
                                <button type="button"
                                        onclick="openPhotoModal({{ $req->id }}, 'before')"
                                        class="flex-1 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-all border border-amber-200">
                                    <i class="fa-solid fa-camera"></i>
                                    قبل ({{ $beforeCount }})
                                </button>
                                <button type="button"
                                        onclick="openPhotoModal({{ $req->id }}, 'after')"
                                        class="flex-1 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-all border border-emerald-200">
                                    <i class="fa-solid fa-check-double"></i>
                                    بعد ({{ $afterCount }})
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-2 mt-auto">
                        <a href="{{ route('client.request-details', $req->id) }}"
                           class="flex-1 min-w-[80px] py-2.5 rounded-xl bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                            <i class="fa-solid fa-eye"></i> عرض
                        </a>

                        @if($isAvailable)
                            @if($myBid)
                                @if($myBid->status === 'pending')
                                    <button type="button"
                                            onclick="openBidModal({{ $req->id }}, {{ $myBid->id }}, {{ $myBid->amount }}, {{ $myBid->duration_days }}, '{{ addslashes($myBid->message ?? '') }}')"
                                            class="flex-1 min-w-[80px] py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                                        <i class="fa-solid fa-pen"></i> عرضي ({{ number_format($myBid->amount) }})
                                    </button>
                                @else
                                    <span class="flex-1 min-w-[80px] py-2.5 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-check"></i> {{ $myBid->status_label }}
                                    </span>
                                @endif
                            @else
                                <button type="button"
                                        onclick="openBidModal({{ $req->id }})"
                                        class="flex-1 min-w-[80px] py-2.5 rounded-xl text-white font-bold text-xs flex items-center justify-center gap-1.5 hover:shadow-glow transition-all"
                                        style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                                    <i class="fa-solid fa-gavel"></i> قدم عرضك
                                </button>
                            @endif
                        @elseif($isMyRequest && $req->status === 'pending')
                            <form method="POST" action="{{ route('craftsman.action') }}" class="flex-1 min-w-[80px]">
                                @csrf
                                <input type="hidden" name="request_id" value="{{ $req->id }}">
                                <button type="submit" name="request_action" value="accept"
                                        class="w-full py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                                    <i class="fa-solid fa-check"></i> قبول
                                </button>
                            </form>
                        @elseif($isMyRequest && $req->status === 'accepted')
                            <form method="POST" action="{{ route('craftsman.action') }}" class="flex-1 min-w-[80px]">
                                @csrf
                                <input type="hidden" name="request_id" value="{{ $req->id }}">
                                <button type="submit" name="request_action" value="start"
                                        class="w-full py-2.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                                    <i class="fa-solid fa-play"></i> بدء
                                </button>
                            </form>
                        @elseif($isMyRequest && $req->status === 'in_progress')
                            <form method="POST" action="{{ route('craftsman.action') }}" class="flex-1 min-w-[80px]"
                                  onsubmit="return confirm('إنهاء الطلب؟')">
                                @csrf
                                <input type="hidden" name="request_id" value="{{ $req->id }}">
                                <button type="submit" name="request_action" value="complete"
                                        class="w-full py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                                    <i class="fa-solid fa-flag-checkered"></i> إنهاء
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
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

                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-3 flex items-start gap-2">
                    <i class="fa-solid fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-xs text-blue-700 leading-relaxed">
                        سيتم إشعار العميل بعرضك فوراً. عند القبول، سيتم ربط الطلب بك تلقائياً.
                    </p>
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
                    <i class="fa-solid fa-paper-plane"></i> إرسال العرض
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════ Photo Upload Modal ═══════════ --}}
<div id="photoModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur z-50 items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full" onclick="event.stopPropagation()">

        <div class="flex items-center justify-between mb-4">
            <h3 class="font-black text-lg text-slate-800">
                <i class="fa-solid fa-camera text-gold-500"></i>
                <span id="photoModalTitle">رفع صور</span>
            </h3>
            <button type="button" onclick="closePhotoModal()"
                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-slate-600"></i>
            </button>
        </div>

        <form method="POST" id="photoForm" action="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" id="photoType">

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">
                        اختر الصور (حتى 5 صور، 5MB لكل صورة) *
                    </label>
                    <input type="file" name="photos[]" multiple accept="image/*" required
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">وصف (اختياري)</label>
                    <textarea name="description" rows="2" maxlength="500"
                              placeholder="ملاحظات على العمل..."
                              class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm resize-none"></textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closePhotoModal()"
                        class="flex-1 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 font-bold text-sm text-slate-700">
                    إلغاء
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-2xl text-white font-bold text-sm flex items-center justify-center gap-2"
                        style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                    <i class="fa-solid fa-cloud-arrow-up"></i> رفع
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ═══════════ Bid Modal ═══════════
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

document.getElementById('bidModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeBidModal();
});

// ═══════════ Photo Modal ═══════════
function openPhotoModal(requestId, type) {
    const form  = document.getElementById('photoForm');
    const title = document.getElementById('photoModalTitle');

    form.action = `/craftsman/requests/${requestId}/work-photos`;
    document.getElementById('photoType').value = type;
    title.textContent = type === 'before' ? '📸 رفع صور قبل التنفيذ' : '✅ رفع صور بعد التنفيذ';

    const m = document.getElementById('photoModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closePhotoModal() {
    const m = document.getElementById('photoModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

document.getElementById('photoModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closePhotoModal();
});

// ═══════════ ESC ═══════════
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeBidModal();
        closePhotoModal();
    }
});
</script>
@endpush
