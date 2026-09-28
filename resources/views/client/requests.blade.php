@extends('layouts.client')
@section('title', 'طلباتي')

@push('styles')
<style>
    /* ============================================
       Dark Content Wrapper
       ============================================ */
    .dark-content-wrapper {
        background: linear-gradient(135deg, #0A1428 0%, #0F1E3C 50%, #152C4A 100%);
        border-radius: 32px;
        padding: 2rem;
        min-height: calc(100vh - 200px);
        position: relative;
        overflow: hidden;
    }

    .dark-content-wrapper::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(212, 162, 76, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .dark-content-wrapper::after {
        content: '';
        position: absolute;
        bottom: -100px;
        left: -100px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* ============================================
       Search Bar
       ============================================ */
    .search-glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1.5px solid rgba(212, 162, 76, 0.3);
        border-radius: 20px;
        padding: 0.75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        backdrop-filter: blur(20px);
        transition: all 0.3s;
    }

    .search-glass:focus-within {
        border-color: rgba(212, 162, 76, 0.6);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 30px rgba(212, 162, 76, 0.2);
    }

    .search-glass input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: white;
        font-size: 0.95rem;
        font-family: 'Cairo', sans-serif;
    }

    .search-glass input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    /* ============================================
       Filter Chips
       ============================================ */
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.25rem;
        border-radius: 16px;
        font-size: 0.85rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.05);
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        transition: all 0.3s;
        font-family: 'Cairo', sans-serif;
    }

    .filter-chip:hover {
        background: rgba(255, 255, 255, 0.08);
        color: white;
    }

    .filter-chip.active {
        background: linear-gradient(135deg, #D4A24C, #B8873A);
        border-color: #D4A24C;
        color: white;
        box-shadow: 0 8px 24px rgba(212, 162, 76, 0.4);
    }

    /* ============================================
       Request Card - Glassmorphism
       ============================================ */
    .req-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.02) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1.5px solid rgba(255, 255, 255, 0.08);
        border-radius: 28px;
        padding: 1.5rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .req-card:hover {
        border-color: rgba(212, 162, 76, 0.4);
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .req-card.expanded {
        border-color: rgba(212, 162, 76, 0.6);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 40px rgba(212, 162, 76, 0.15);
    }

    /* ============================================
       Category Icon Box
       ============================================ */
    .cat-icon-box {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(212, 162, 76, 0.2), rgba(212, 162, 76, 0.05));
        border: 1.5px solid rgba(212, 162, 76, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #E5B968;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    /* ============================================
       Status Badge
       ============================================ */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .status-badge.pending {
        background: rgba(251, 191, 36, 0.15);
        color: #FCD34D;
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .status-badge.accepted {
        background: rgba(59, 130, 246, 0.15);
        color: #60A5FA;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .status-badge.in_progress {
        background: rgba(139, 92, 246, 0.15);
        color: #A78BFA;
        border: 1px solid rgba(139, 92, 246, 0.3);
    }

    .status-badge.completed {
        background: rgba(16, 185, 129, 0.15);
        color: #34D399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .status-badge.cancelled {
        background: rgba(239, 68, 68, 0.15);
        color: #F87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    /* ============================================
       Craftsman Avatar Row
       ============================================ */
    .craftsman-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #D4A24C;
    }

    /* ============================================
       Action Buttons
       ============================================ */
    .btn-glass {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.6rem 1.25rem;
        border-radius: 14px;
        font-size: 0.8rem;
        font-weight: 800;
        background: rgba(255, 255, 255, 0.05);
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.85);
        transition: all 0.3s;
        cursor: pointer;
        text-decoration: none;
        font-family: 'Cairo', sans-serif;
    }

    .btn-glass:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(212, 162, 76, 0.5);
        color: #E5B968;
        transform: translateY(-2px);
    }

    .btn-glass.gold {
        background: linear-gradient(135deg, #D4A24C, #B8873A);
        border-color: transparent;
        color: white;
    }

    .btn-glass.gold:hover {
        background: linear-gradient(135deg, #E5B968, #D4A24C);
        box-shadow: 0 8px 24px rgba(212, 162, 76, 0.4);
        color: white;
    }

    .btn-glass.green {
        background: linear-gradient(135deg, #10B981, #047857);
        border-color: transparent;
        color: white;
    }

    .btn-glass.green:hover {
        background: linear-gradient(135deg, #34D399, #10B981);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
        color: white;
    }

    .btn-glass.red {
        background: linear-gradient(135deg, #EF4444, #991B1B);
        border-color: transparent;
        color: white;
    }

    .btn-glass.red:hover {
        background: linear-gradient(135deg, #F87171, #EF4444);
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.4);
        color: white;
    }

    /* ============================================
       Details Box
       ============================================ */
    .detail-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.85rem;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-row i {
        color: #D4A24C;
        width: 20px;
        text-align: center;
    }

    .detail-row strong {
        color: white;
        font-weight: 700;
    }

    /* ============================================
       Previous Ratings Box
       ============================================ */
    .prev-ratings-box {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 1rem;
    }

    .rating-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.35rem 0;
        font-size: 0.8rem;
    }

    .rating-item .label {
        color: rgba(255, 255, 255, 0.6);
    }

    .rating-item .value {
        color: #FCD34D;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* ============================================
       Empty State
       ============================================ */
    .empty-state-dark {
        text-align: center;
        padding: 4rem 2rem;
        color: rgba(255, 255, 255, 0.5);
    }

    /* ============================================
       Responsive
       ============================================ */
    @media (max-width: 768px) {
        .dark-content-wrapper {
            padding: 1rem;
            border-radius: 20px;
        }
    }
</style>
@endpush

@section('content')

<div class="dark-content-wrapper">

    {{-- ============================================
         Page Header
         ============================================ --}}
    <div class="relative z-10 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
            <div>
                <h1 class="text-3xl lg:text-4xl font-black text-white flex items-center gap-3">
                    <i class="fa-solid fa-clipboard-list text-gold-400"></i>
                    الطلبات
                </h1>
                <p class="text-white/60 text-sm mt-1">
                    {{ $stats['total'] }} طلب إجمالي
                </p>
            </div>

            {{-- New Request Button --}}
            <a href="{{ route('client.request-form') }}"
               class="btn-glass gold">
                <i class="fa-solid fa-plus"></i>
                طلب جديد
            </a>
        </div>

        {{-- ============================================
             Search Bar
             ============================================ --}}
        <div class="search-glass mb-5">
            <i class="fa-solid fa-magnifying-glass text-gold-400"></i>
            <input type="text"
                   id="searchInput"
                   placeholder="ابحث في الطلبات..."
                   oninput="filterCards()">
            <i class="fa-solid fa-sliders text-white/40 cursor-pointer hover:text-gold-400 transition-colors"></i>
        </div>

        {{-- ============================================
             Filter Chips
             ============================================ --}}
        <div class="flex flex-wrap gap-2">
            <button type="button" class="filter-chip active" data-filter="all" onclick="setFilter('all', this)">
                <i class="fa-solid fa-layer-group"></i>
                الكل
                <span class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]">{{ $stats['total'] }}</span>
            </button>

            <button type="button" class="filter-chip" data-filter="pending" onclick="setFilter('pending', this)">
                <i class="fa-solid fa-clock"></i>
                قيد الانتظار
                <span class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]">{{ $stats['pending'] }}</span>
            </button>

            <button type="button" class="filter-chip" data-filter="in_progress" onclick="setFilter('in_progress', this)">
                <i class="fa-solid fa-spinner"></i>
                قيد التنفيذ
                <span class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]">{{ $stats['in_progress'] }}</span>
            </button>

            <button type="button" class="filter-chip" data-filter="completed" onclick="setFilter('completed', this)">
                <i class="fa-solid fa-circle-check"></i>
                مكتمل
                <span class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]">{{ $stats['completed'] }}</span>
            </button>

            <button type="button" class="filter-chip" data-filter="cancelled" onclick="setFilter('cancelled', this)">
                <i class="fa-solid fa-xmark"></i>
                ملغي
                <span class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]">{{ $stats['cancelled'] }}</span>
            </button>
        </div>
    </div>

    {{-- ============================================
         Requests Cards Grid
         ============================================ --}}
    <div class="relative z-10" id="requestsGrid">

        @if($requests->isEmpty())
            <div class="empty-state-dark">
                <div class="w-24 h-24 mx-auto rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-inbox text-4xl text-white/30"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">لا توجد طلبات</h3>
                <p class="text-sm text-white/50 mb-6">ابدأ بطلب خدمتك الأولى الآن</p>
                <a href="{{ route('client.request-form') }}" class="btn-glass gold">
                    <i class="fa-solid fa-plus"></i>
                    اطلب خدمة
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                @foreach($requests as $req)
                    <div class="req-card"
                         data-status="{{ $req->status }}"
                         data-search="{{ strtolower($req->title . ' ' . ($req->craftsman->full_name ?? '') . ' ' . ($req->category->name ?? '')) }}"
                         x-data="{ expanded: false }">

                        {{-- Card Header --}}
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="cat-icon-box">
                                <i class="fa-solid {{ $req->category->icon ?? 'fa-wrench' }}"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-white text-base leading-tight line-clamp-2 mb-1">
                                    {{ $req->title }}
                                </h3>
                                <p class="text-xs text-white/50">
                                    #{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}
                                </p>
                            </div>
                        </div>

                        {{-- Craftsman Row --}}
                        @if($req->craftsman)
                            <div class="flex items-center gap-2 mb-4">
                                <img src="{{ $req->craftsman->avatar_url }}"
                                     alt="{{ $req->craftsman->full_name }}"
                                     class="craftsman-avatar">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-white truncate">
                                        {{ $req->craftsman->full_name }}
                                    </p>
                                    <p class="text-xs text-white/50">
                                        منذ {{ $req->created_at->diffForHumans(null, true) }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-10 h-10 rounded-full bg-white/5 border border-dashed border-white/20 flex items-center justify-center">
                                    <i class="fa-solid fa-user-clock text-white/40 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white/60">في انتظار القبول</p>
                                    <p class="text-xs text-white/40">منذ {{ $req->created_at->diffForHumans(null, true) }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Price + Status --}}
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs text-white/40">السعر</p>
                                <p class="text-lg font-black text-gold-400">
                                    {{ number_format($req->budget ?? 0) }}
                                    <span class="text-xs text-white/40">ريال</span>
                                </p>
                            </div>
                            <span class="status-badge {{ $req->status }}">
                                @php
                                    $statusIcon = match($req->status) {
                                        'pending'     => 'fa-clock',
                                        'accepted'    => 'fa-handshake',
                                        'in_progress' => 'fa-spinner',
                                        'completed'   => 'fa-circle-check',
                                        'cancelled'   => 'fa-xmark',
                                        default       => 'fa-circle',
                                    };
                                    $statusText = match($req->status) {
                                        'pending'     => 'قيد الانتظار',
                                        'accepted'    => 'مقبول',
                                        'in_progress' => 'قيد التنفيذ',
                                        'completed'   => 'مكتمل',
                                        'cancelled'   => 'ملغي',
                                        default       => 'غير معروف',
                                    };
                                @endphp
                                <i class="fa-solid {{ $statusIcon }}"></i>
                                {{ $statusText }}
                            </span>
                        </div>

                        {{-- Expandable Details --}}
                        <div x-show="expanded"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-4 mt-4 pt-4 border-t border-white/10">

                            {{-- Details Box --}}
                            <div class="prev-ratings-box">
                                <div class="detail-row">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <div>
                                        <span class="text-white/50 text-xs block">العنوان</span>
                                        <strong>{{ $req->address }}</strong>
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <i class="fa-solid fa-calendar"></i>
                                    <div>
                                        <span class="text-white/50 text-xs block">التاريخ المقترح</span>
                                        <strong>{{ $req->preferred_date->format('Y-m-d') }}</strong>
                                    </div>
                                </div>
                                @if($req->is_emergency)
                                    <div class="detail-row">
                                        <i class="fa-solid fa-bolt text-red-400"></i>
                                        <strong class="text-red-400">طلب طوارئ</strong>
                                    </div>
                                @endif
                                @if($req->has_warranty)
                                    <div class="detail-row">
                                        <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                                        <strong class="text-emerald-400">ضمان حتى {{ $req->warranty_end_date?->format('Y-m-d') }}</strong>
                                    </div>
                                @endif
                            </div>

                            {{-- Previous Ratings --}}
                            @if($req->craftsman && $req->craftsman->craftsmanProfile)
                                @php $profile = $req->craftsman->craftsmanProfile; @endphp
                                <div class="prev-ratings-box">
                                    <p class="text-xs font-bold text-white/60 mb-3">
                                        <i class="fa-solid fa-star text-gold-400"></i>
                                        التقييمات السابقة
                                    </p>
                                    <div class="rating-item">
                                        <span class="label">🌟 الجودة</span>
                                        <span class="value">{{ number_format($profile->rating_avg ?? 0, 1) }}</span>
                                    </div>
                                    <div class="rating-item">
                                        <span class="label">⏰ الالتزام</span>
                                        <span class="value">{{ number_format($profile->rating_avg ?? 0, 1) }}</span>
                                    </div>
                                    <div class="rating-item">
                                        <span class="label">🤝 التعامل</span>
                                        <span class="value">{{ number_format($profile->rating_avg ?? 0, 1) }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="flex flex-wrap gap-2">
                                @if($req->status === 'pending')
                                    <a href="{{ route('client.checkout', $req->id) }}" class="btn-glass gold">
                                        <i class="fa-solid fa-credit-card"></i>
                                        ادفع الآن
                                    </a>
                                @endif

                                @if($req->status === 'completed' && !$req->review)
                                    <a href="{{ route('client.rate-craftsman', $req->id) }}" class="btn-glass green">
                                        <i class="fa-solid fa-star"></i>
                                        تقييم
                                    </a>
                                @endif

                                @if(in_array($req->status, ['accepted', 'in_progress']) && $req->craftsman_id)
                                    <a href="{{ route('client.chat', $req->id) }}" class="btn-glass">
                                        <i class="fa-solid fa-comments"></i>
                                        مراسلة
                                    </a>
                                @endif

                                <a href="{{ route('client.request-details', $req->id) }}" class="btn-glass">
                                    <i class="fa-solid fa-eye"></i>
                                    التفاصيل
                                </a>
                            </div>
                        </div>

                        {{-- Expand Button --}}
                        <button type="button"
                                @click="expanded = !expanded"
                                class="w-full mt-4 py-2.5 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-gold-400 text-sm font-bold transition-all flex items-center justify-center gap-2">
                            <span x-text="expanded ? 'إخفاء التفاصيل' : 'عرض التفاصيل'"></span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform"
                               :class="expanded ? 'rotate-180' : ''"></i>
                        </button>

                    </div>
                @endforeach

            </div>

            {{-- No Results Message (Filter) --}}
            <div id="noResults" class="hidden empty-state-dark">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-magnifying-glass text-3xl text-white/30"></i>
                </div>
                <p class="text-white font-bold">لا توجد نتائج مطابقة</p>
            </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script>
    let currentFilter = 'all';

    function setFilter(filter, btn) {
        currentFilter = filter;

        // Update active state
        document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');

        filterCards();
    }

    function filterCards() {
        const search = document.getElementById('searchInput').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.req-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const status = card.dataset.status;
            const searchData = card.dataset.search;

            const matchFilter = currentFilter === 'all' || status === currentFilter;
            const matchSearch = search === '' || searchData.includes(search);

            if (matchFilter && matchSearch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResults');
        if (noResults) {
            if (visibleCount === 0 && cards.length > 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
    }
</script>
@endpush
