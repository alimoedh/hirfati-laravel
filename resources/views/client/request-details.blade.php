@extends('layouts.client')
@section('title', 'تفاصيل الطلب #' . str_pad($request->id, 4, '0', STR_PAD_LEFT))

@push('styles')
<style>
    /* ============================================
       Dark Wrapper
       ============================================ */
    .dark-wrapper {
        background: linear-gradient(135deg, #0A1428 0%, #0F1E3C 50%, #152C4A 100%);
        border-radius: 32px;
        padding: 2rem;
        min-height: calc(100vh - 200px);
        position: relative;
        overflow: hidden;
    }

    .dark-wrapper::before {
        content: '';
        position: absolute;
        top: -150px;
        right: -150px;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(212, 162, 76, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .dark-wrapper::after {
        content: '';
        position: absolute;
        bottom: -150px;
        left: -150px;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* ============================================
       Horizontal Timeline
       ============================================ */
    .timeline-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        position: relative;
        padding: 1.5rem 0;
    }

    .timeline-step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .timeline-step .step-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.4);
        transition: all 0.4s;
        margin-bottom: 0.75rem;
    }

    .timeline-step .step-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.4);
        transition: all 0.4s;
    }

    .timeline-step.completed .step-icon {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.5);
        color: #34D399;
    }

    .timeline-step.completed .step-label {
        color: #34D399;
    }

    .timeline-step.active .step-icon {
        background: linear-gradient(135deg, #D4A24C, #B8873A);
        border-color: #E5B968;
        color: white;
        box-shadow: 0 0 30px rgba(212, 162, 76, 0.5), 0 0 0 8px rgba(212, 162, 76, 0.15);
        animation: pulse-gold 2s infinite;
    }

    .timeline-step.active .step-label {
        color: #E5B968;
    }

    .timeline-connector {
        flex: 1;
        height: 2px;
        background: rgba(255, 255, 255, 0.08);
        position: relative;
        margin-bottom: 2rem;
        z-index: 1;
    }

    .timeline-connector.completed {
        background: linear-gradient(90deg, #10B981, #D4A24C);
    }

    @keyframes pulse-gold {
        0%, 100% {
            box-shadow: 0 0 30px rgba(212, 162, 76, 0.5), 0 0 0 8px rgba(212, 162, 76, 0.15);
        }
        50% {
            box-shadow: 0 0 40px rgba(212, 162, 76, 0.8), 0 0 0 14px rgba(212, 162, 76, 0.05);
        }
    }

    /* ============================================
       Glass Cards
       ============================================ */
    .glass-card-dark {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.02) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1.5px solid rgba(255, 255, 255, 0.08);
        border-radius: 28px;
        padding: 1.5rem;
        transition: all 0.3s;
    }

    .glass-card-dark:hover {
        border-color: rgba(212, 162, 76, 0.3);
    }

    /* ============================================
       Craftsman Card (3D)
       ============================================ */
    .craftsman-3d {
        text-align: center;
        padding: 1.5rem;
    }

    .craftsman-avatar-wrap {
        position: relative;
        display: inline-block;
        margin-bottom: 1rem;
    }

    .craftsman-avatar-lg {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #D4A24C;
        box-shadow: 0 0 30px rgba(212, 162, 76, 0.4);
    }

    .verified-badge {
        position: absolute;
        bottom: 5px;
        left: 5px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3B82F6, #1E40AF);
        border: 3px solid #0F1E3C;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
    }

    .craftsman-action-btn {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 0.4rem;
        padding: 0.85rem;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.05);
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.85);
        transition: all 0.3s;
        text-decoration: none;
        flex: 1;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .craftsman-action-btn:hover {
        background: rgba(212, 162, 76, 0.15);
        border-color: rgba(212, 162, 76, 0.5);
        color: #E5B968;
        transform: translateY(-3px);
    }

    .craftsman-action-btn i {
        font-size: 1.1rem;
    }

    /* ============================================
       Detail Row
       ============================================ */
    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 0.85rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-item-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(212, 162, 76, 0.15);
        border: 1px solid rgba(212, 162, 76, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #E5B968;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .detail-item-label {
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.4);
        font-weight: 700;
        margin-bottom: 0.15rem;
    }

    .detail-item-value {
        font-size: 0.9rem;
        color: white;
        font-weight: 700;
    }

    /* ============================================
       Image Gallery
       ============================================ */
    .image-gallery {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .image-gallery::-webkit-scrollbar {
        height: 6px;
    }

    .image-gallery::-webkit-scrollbar-thumb {
        background: rgba(212, 162, 76, 0.3);
        border-radius: 3px;
    }

    .gallery-thumb {
        width: 100px;
        height: 100px;
        border-radius: 20px;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.3s;
    }

    .gallery-thumb:hover {
        border-color: #D4A24C;
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(212, 162, 76, 0.4);
    }

    /* ============================================
       Map Container
       ============================================ */
    .map-container {
        height: 200px;
        border-radius: 20px;
        overflow: hidden;
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        position: relative;
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        filter: grayscale(30%) brightness(0.85);
    }

    /* ============================================
       Chat Preview
       ============================================ */
    .chat-bubble {
        padding: 0.6rem 0.9rem;
        border-radius: 14px;
        font-size: 0.8rem;
        max-width: 85%;
        margin-bottom: 0.5rem;
    }

    .chat-bubble.received {
        background: rgba(212, 162, 76, 0.15);
        border: 1px solid rgba(212, 162, 76, 0.3);
        color: #FCD34D;
        margin-left: auto;
        border-bottom-left-radius: 4px;
    }

    .chat-bubble.sent {
        background: rgba(59, 130, 246, 0.15);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #93C5FD;
        margin-right: auto;
        border-bottom-right-radius: 4px;
    }

    /* ============================================
       Action Buttons
       ============================================ */
    .btn-glass-lg {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.85rem 1.5rem;
        border-radius: 16px;
        font-size: 0.85rem;
        font-weight: 800;
        background: rgba(255, 255, 255, 0.05);
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.85);
        transition: all 0.3s;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-glass-lg:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(212, 162, 76, 0.5);
        color: #E5B968;
    }

    .btn-glass-lg.gold {
        background: linear-gradient(135deg, #D4A24C, #B8873A);
        border: none;
        color: white;
    }

    .btn-glass-lg.gold:hover {
        box-shadow: 0 8px 24px rgba(212, 162, 76, 0.5);
        color: white;
    }

    .btn-glass-lg.green {
        background: linear-gradient(135deg, #10B981, #047857);
        border: none;
        color: white;
    }

    .btn-glass-lg.green:hover {
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.5);
        color: white;
    }

    .btn-glass-lg.red {
        background: rgba(239, 68, 68, 0.15);
        border: 1.5px solid rgba(239, 68, 68, 0.4);
        color: #F87171;
    }

    .btn-glass-lg.red:hover {
        background: rgba(239, 68, 68, 0.25);
        color: #FCA5A5;
    }
</style>
@endpush

@section('content')

<div class="dark-wrapper">

    {{-- ============================================
         Page Header
         ============================================ --}}
    <div class="relative z-10 mb-6">
        <a href="{{ route('client.requests') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-gold-400 text-sm font-bold mb-4 transition-colors">
            <i class="fa-solid fa-arrow-right"></i>
            العودة إلى الطلبات
        </a>

        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl lg:text-3xl font-black text-white mb-2">
                    {{ $request->title }}
                </h1>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-white/50 text-sm font-bold">
                        #{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="status-badge {{ $request->status }}" style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.35rem 0.85rem; border-radius:12px; font-size:0.75rem; font-weight:800;
                        @if($request->status === 'pending')
                            background: rgba(251, 191, 36, 0.15); color: #FCD34D; border: 1px solid rgba(251, 191, 36, 0.3);
                        @elseif($request->status === 'in_progress')
                            background: rgba(139, 92, 246, 0.15); color: #A78BFA; border: 1px solid rgba(139, 92, 246, 0.3);
                        @elseif($request->status === 'completed')
                            background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3);
                        @elseif($request->status === 'accepted')
                            background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.3);
                        @else
                            background: rgba(239, 68, 68, 0.15); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.3);
                        @endif">
                        @if($request->status === 'pending') ⏳ قيد الانتظار
                        @elseif($request->status === 'accepted') 🤝 مقبول
                        @elseif($request->status === 'in_progress') 🔄 قيد التنفيذ
                        @elseif($request->status === 'completed') ✅ مكتمل
                        @else ❌ ملغي
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================
         Horizontal Timeline
         ============================================ --}}
    <div class="glass-card-dark relative z-10 mb-6">
        <div class="timeline-container">
            @php
                $stages = [
                    ['key' => 'pending',     'label' => 'إرسال الطلب',    'icon' => 'fa-paper-plane'],
                    ['key' => 'accepted',    'label' => 'القبول',        'icon' => 'fa-handshake'],
                    ['key' => 'in_progress', 'label' => 'التنفيذ',        'icon' => 'fa-screwdriver-wrench'],
                    ['key' => 'completed',   'label' => 'الإنهاء',        'icon' => 'fa-flag-checkered'],
                ];
                $statusOrder = ['pending' => 0, 'accepted' => 1, 'in_progress' => 2, 'completed' => 3, 'cancelled' => -1];
                $currentIndex = $statusOrder[$request->status] ?? 0;
            @endphp

            @foreach($stages as $i => $stage)
                @php
                    $class = '';
                    if ($i < $currentIndex) $class = 'completed';
                    elseif ($i === $currentIndex) $class = 'active';
                @endphp

                <div class="timeline-step {{ $class }}">
                    <div class="step-icon">
                        <i class="fa-solid {{ $stage['icon'] }}"></i>
                    </div>
                    <div class="step-label">{{ $stage['label'] }}</div>
                </div>

                @if($i < count($stages) - 1)
                    <div class="timeline-connector {{ $i < $currentIndex ? 'completed' : '' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ============================================
         Main Content Grid
         ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative z-10">

      {{-- ============================================
     Left Column: Craftsman Card
     ============================================ --}}
<div class="lg:col-span-1">
    <div class="glass-card-dark craftsman-3d">

        @if($request->craftsman)
            <div class="craftsman-avatar-wrap">
                <img src="{{ $request->craftsman->avatar_url }}"
                     alt="{{ $request->craftsman->full_name }}"
                     class="craftsman-avatar-lg">
                @if($request->craftsman->is_verified)
                    <div class="verified-badge">
                        <i class="fa-solid fa-check"></i>
                    </div>
                @endif
            </div>

            <h3 class="text-xl font-black text-white mb-1">
                {{ $request->craftsman->full_name }}
            </h3>

            <div class="inline-flex items-center gap-1.5 bg-amber-500/15 border border-amber-500/30 px-3 py-1 rounded-xl mb-5">
                <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                <span class="text-amber-300 text-sm font-bold">
                    {{ number_format($request->craftsman->craftsmanProfile->rating_avg ?? 0, 1) }}
                </span>
                <span class="text-amber-400/60 text-xs">
                    ({{ $request->craftsman->craftsmanProfile->total_reviews ?? 0 }})
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('client.chat', $request->id) }}" class="craftsman-action-btn">
                    <i class="fa-solid fa-comments"></i>
                    <span>مراسلة</span>
                </a>

                <a href="tel:{{ $request->craftsman->phone }}" class="craftsman-action-btn">
                    <i class="fa-solid fa-phone"></i>
                    <span>اتصال</span>
                </a>

                <a href="{{ route('client.live-stream.show', $request->id) }}" class="craftsman-action-btn">
                    <i class="fa-solid fa-video"></i>
                    <span>بث مباشر</span>
                </a>
            </div>
        @else
            {{-- No Craftsman Yet --}}
            <div class="craftsman-avatar-wrap">
                <div class="w-[110px] h-[110px] rounded-full border-4 border-dashed border-white/20 flex items-center justify-center bg-white/5">
                    <i class="fa-solid fa-user-clock text-4xl text-white/30"></i>
                </div>
            </div>
            <h3 class="text-xl font-black text-white/60 mb-2">في انتظار القبول</h3>
            <p class="text-white/40 text-sm">سيتم إشعار الحرفيين المتاحين</p>
        @endif
    </div>

    {{-- 🆕 الملف العام للحرفي --}}
    @if($request->craftsman)
        <a href="{{ $request->craftsman->publicProfileUrl() }}" target="_blank"
           class="mt-3 w-full py-3 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-gold-400/50 text-white/80 hover:text-gold-400 font-bold text-sm flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-external-link-alt"></i>
            عرض الملف العام للحرفي
        </a>
    @endif
</div>

        {{-- ============================================
             Right Column: Details
             ============================================ --}}
        <div class="lg:col-span-2">
            <div class="glass-card-dark">
                <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-gold-400"></i>
                    تفاصيل الطلب
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                    {{-- Date --}}
                    <div class="detail-item">
                        <div class="detail-item-icon">
                            <i class="fa-solid fa-calendar"></i>
                        </div>
                        <div>
                            <p class="detail-item-label">التاريخ المقترح</p>
                            <p class="detail-item-value">{{ $request->preferred_date->format('d / m / Y') }}</p>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="detail-item">
                        <div class="detail-item-icon">
                            <i class="fa-solid fa-tag"></i>
                        </div>
                        <div>
                            <p class="detail-item-label">التصنيف</p>
                            <p class="detail-item-value">{{ $request->category->name ?? 'غير محدد' }}</p>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="detail-item md:col-span-2">
                        <div class="detail-item-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <p class="detail-item-label">العنوان</p>
                            <p class="detail-item-value">{{ $request->address }}</p>
                        </div>
                    </div>

                    {{-- Budget --}}
                    <div class="detail-item">
                        <div class="detail-item-icon">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <p class="detail-item-label">السعر</p>
                            <p class="detail-item-value text-gold-400">
                                {{ number_format($request->budget ?? 0) }} ر.ي
                            </p>
                        </div>
                    </div>

                    {{-- Time --}}
                    @if($request->preferred_time)
                        <div class="detail-item">
                            <div class="detail-item-icon">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <p class="detail-item-label">الوقت</p>
                                <p class="detail-item-value">{{ $request->preferred_time }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Warranty --}}
                    @if($request->has_warranty)
                        <div class="detail-item">
                            <div class="detail-item-icon" style="background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.25); color: #34D399;">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <p class="detail-item-label">الضمان</p>
                                <p class="detail-item-value text-emerald-400">
                                    حتى {{ $request->warranty_end_date?->format('Y-m-d') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Emergency --}}
                    @if($request->is_emergency)
                        <div class="detail-item">
                            <div class="detail-item-icon" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.25); color: #F87171;">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <div>
                                <p class="detail-item-label">الحالة</p>
                                <p class="detail-item-value text-red-400">🚨 طوارئ</p>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Description --}}
                <div class="mt-5 pt-5 border-t border-white/5">
                    <p class="text-white/50 text-xs font-bold mb-2">وصف المشكلة</p>
                    <p class="text-white/80 text-sm leading-relaxed">
                        {{ $request->description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
{{-- ═══════════ صور العمل (Before/After) ═══════════ --}}
@if($request->workPhotos->isNotEmpty())
    <div class="glass-card-dark mt-6 relative z-10">
        <h3 class="text-lg font-black text-white mb-5 flex items-center gap-2">
            <i class="fa-solid fa-camera-retro text-gold-400"></i>
            توثيق العمل
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- قبل --}}
            @php $beforePhotos = $request->workPhotos->where('type', 'before'); @endphp
            @if($beforePhotos->isNotEmpty())
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <h4 class="font-bold text-amber-300 text-sm">📸 قبل التنفيذ</h4>
                        <span class="text-white/40 text-xs">({{ $beforePhotos->count() }})</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($beforePhotos as $photo)
                            <img src="{{ $photo->image_url }}"
                                 onclick="window.open(this.src)"
                                 class="w-full h-32 object-cover rounded-xl border-2 border-amber-400/30 cursor-pointer hover:border-amber-400 transition-all">
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- بعد --}}
            @php $afterPhotos = $request->workPhotos->where('type', 'after'); @endphp
            @if($afterPhotos->isNotEmpty())
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <h4 class="font-bold text-emerald-300 text-sm">✅ بعد التنفيذ</h4>
                        <span class="text-white/40 text-xs">({{ $afterPhotos->count() }})</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($afterPhotos as $photo)
                            <img src="{{ $photo->image_url }}"
                                 onclick="window.open(this.src)"
                                 class="w-full h-32 object-cover rounded-xl border-2 border-emerald-400/30 cursor-pointer hover:border-emerald-400 transition-all">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif


    {{-- ============================================
         Problem Images Gallery
         ============================================ --}}
    @if($request->problem_image)
        <div class="glass-card-dark mt-6 relative z-10">
            <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-images text-gold-400"></i>
                Problem Images Gallery
            </h3>

            <div class="image-gallery">
                <img src="{{ $request->problem_image_url }}"
                     alt="Problem Image"
                     class="gallery-thumb"
                     onclick="window.open(this.src, '_blank')">
                {{-- نكرر الصورة للعرض في المعرض --}}
                <img src="{{ $request->problem_image_url }}" class="gallery-thumb" onclick="window.open(this.src, '_blank')">
                <img src="{{ $request->problem_image_url }}" class="gallery-thumb" onclick="window.open(this.src, '_blank')">
            </div>
        </div>
    @endif

    {{-- ============================================
         Map + Chat Preview
         ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 relative z-10">

        {{-- Map --}}
        <div class="glass-card-dark">
            <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-map-location-dot text-gold-400"></i>
                الموقع
            </h3>
            <div class="map-container">
                <iframe
                    src="https://www.openstreetmap.org/export/embed.html?bbox=44.19%2C15.32%2C44.24%2C15.38&layer=mapnik&marker=15.3694%2C44.1910"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <a href="https://www.openstreetmap.org/?mlat=15.3694&mlon=44.1910#map=15/15.3694/44.1910"
               target="_blank"
               class="btn-glass-lg w-full mt-4">
                <i class="fa-solid fa-external-link-alt"></i>
                Open Map
            </a>
        </div>

        {{-- Chat Preview --}}
        <div class="glass-card-dark">
            <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-comments text-gold-400"></i>
                Chat Preview
            </h3>

            <div class="space-y-2 mb-4 min-h-[150px]">
                @php
                    $previewMessages = \App\Models\Message::where('request_id', $request->id)
                        ->with('sender:id,full_name')
                        ->latest()
                        ->limit(4)
                        ->get()
                        ->reverse();
                @endphp

                @forelse($previewMessages as $msg)
                    <div class="chat-bubble {{ $msg->sender_id === auth()->id() ? 'sent' : 'received' }}">
                        <p class="font-bold mb-0.5 text-[10px] opacity-70">{{ $msg->sender->full_name ?? '' }}</p>
                        {{ \Illuminate\Support\Str::limit($msg->message, 60) }}
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fa-solid fa-comment-dots text-4xl text-white/20 mb-2"></i>
                        <p class="text-white/40 text-sm">لا توجد رسائل بعد</p>
                    </div>
                @endforelse
            </div>

            @if($request->craftsman)
                <a href="{{ route('client.chat', $request->id) }}" class="btn-glass-lg gold w-full">
                    <i class="fa-solid fa-comments"></i>
                    فتح المحادثة
                </a>
            @endif
        </div>
    </div>

    {{-- ============================================
         Action Buttons
         ============================================ --}}
    <div class="mt-6 flex flex-wrap gap-3 relative z-10">
            @if(in_array($request->status, ['accepted', 'in_progress', 'completed']))
        <a href="{{ $isCraftsman ?? false ? route('craftsman.invoice', $request->id) : route('client.invoice', $request->id) }}"
           class="btn-glass-lg" style="background: linear-gradient(135deg, #10B981, #059669); color: white; border: none;">
            <i class="fa-solid fa-file-pdf"></i>
            فاتورة PDF
        </a>
    @endif


        @if($request->status === 'pending' && $request->craftsman_id)
            <a href="{{ route('client.checkout', $request->id) }}" class="btn-glass-lg gold">
                <i class="fa-solid fa-credit-card"></i>
                ادفع الآن ({{ number_format($request->budget ?? 0) }} ريال)
            </a>
        @endif

        @if($request->status === 'completed' && !$request->review)
            <a href="{{ route('client.rate-craftsman', $request->id) }}" class="btn-glass-lg green">
                <i class="fa-solid fa-star"></i>
                تقييم الحرفي
            </a>
        @endif

        @if(in_array($request->status, ['pending', 'accepted']))
            <form method="POST" action="{{ route('client.request.cancel', $request->id) }}" class="inline"
                  onsubmit="return confirm('هل أنت متأكد من إلغاء الطلب؟')">
                @csrf
                <button type="submit" class="btn-glass-lg red">
                    <i class="fa-solid fa-xmark"></i>
                    إلغاء الطلب
                </button>
            </form>
        @endif

        @if(in_array($request->status, ['completed', 'in_progress', 'accepted']))
            <a href="{{ route('client.complaint-form') }}?request_id={{ $request->id }}" class="btn-glass-lg red">
                <i class="fa-solid fa-triangle-exclamation"></i>
                تقديم شكوى
            </a>
        @endif
{{-- ═══════════ العروض (Bidding) ═══════════ --}}
@if($request->status === 'pending' && !$request->craftsman_id)
    @php
        $bids = \App\Models\Bid::with('craftsman.craftsmanProfile')
            ->where('request_id', $request->id)
            ->where('status', 'pending')
            ->orderBy('amount')
            ->get();
    @endphp

    <div class="glass-card-dark mt-6 relative z-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-gavel text-gold-400"></i>
                العروض المقدمة ({{ $bids->count() }})
            </h3>
            <span class="text-xs text-white/50">مرتبة من الأرخص للأغلى</span>
        </div>

        @if($bids->isEmpty())
            <div class="text-center py-10">
                <i class="fa-solid fa-hourglass-half text-4xl text-white/30 mb-3"></i>
                <p class="text-white/60 text-sm">في انتظار عروض الحرفيين...</p>
                <p class="text-white/40 text-xs mt-2">سيتم إشعارك عند وصول أول عرض</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($bids as $bid)
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/10 hover:border-gold-400/50 transition-all">
                        <div class="flex items-center gap-4 flex-wrap">
                            {{-- صورة الحرفي --}}
                            <img src="{{ $bid->craftsman->avatar_url }}" 
                                 class="w-14 h-14 rounded-full border-2 border-gold-400 object-cover">

                            {{-- معلومات الحرفي --}}
                            <div class="flex-1 min-w-[150px]">
                                <p class="font-bold text-white text-sm">{{ $bid->craftsman->full_name }}</p>
                                <p class="text-xs text-white/50 mt-1">
                                    <i class="fa-solid fa-star text-amber-400"></i>
                                    {{ number_format($bid->craftsman->craftsmanProfile->rating_avg ?? 0, 1) }}
                                    · {{ $bid->craftsman->craftsmanProfile->experience_years ?? 0 }} سنوات خبرة
                                </p>
                                @if($bid->message)
                                    <p class="text-xs text-white/70 mt-2 italic">"{{ $bid->message }}"</p>
                                @endif
                            </div>

                            {{-- السعر والمدة --}}
                            <div class="text-center">
                                <p class="text-2xl font-black text-gold-400">{{ number_format($bid->amount) }}</p>
                                <p class="text-xs text-white/50">ريال</p>
                            </div>

                            <div class="text-center">
                                <p class="text-lg font-black text-white">{{ $bid->duration_days }}</p>
                                <p class="text-xs text-white/50">أيام</p>
                            </div>

                            {{-- أزرار --}}
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('client.bids.accept', $bid->id) }}" class="inline"
                                      onsubmit="return confirm('قبول عرض {{ $bid->craftsman->full_name }} بمبلغ {{ number_format($bid->amount) }} ريال؟')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs">
                                        <i class="fa-solid fa-check"></i> قبول
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('client.bids.reject', $bid->id) }}" class="inline"
                                      onsubmit="return confirm('رفض هذا العرض؟')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-500/20 hover:bg-red-500/30 text-red-300 font-bold text-xs border border-red-500/30">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

    </div>

</div>

@endsection
