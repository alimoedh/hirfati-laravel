<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $craftsman->full_name }} | {{ config('hirfati.site_name') }}</title>
    <meta name="description" content="{{ Str::limit($craftsman->craftsmanProfile->bio ?? 'حرفي محترف', 155) }}">

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-cairo bg-slate-50 min-h-screen">

{{-- ═══════════ Navbar ═══════════ --}}
<nav class="bg-brand-800 text-white sticky top-0 z-40 shadow-lg">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <img src="{{ asset('assets/images/logo.png') }}" class="h-10 w-auto" alt="حرفتي">
        </a>
        <div class="flex items-center gap-3">
            @auth
                @php
                    $dashUrl = match(auth()->user()->role) {
                        'admin'     => route('admin.dashboard'),
                        'craftsman' => route('craftsman.dashboard'),
                        default     => route('client.dashboard'),
                    };
                @endphp
                <a href="{{ $dashUrl }}" class="text-sm font-bold text-white/80 hover:text-gold-400 transition">
                    <i class="fa-solid fa-gauge-high"></i> لوحة التحكم
                </a>
            @else
                <a href="{{ route('auth.login') }}" class="text-sm font-bold text-white/80 hover:text-gold-400 transition">
                    تسجيل الدخول
                </a>
                <a href="{{ route('auth.register') }}"
                   class="px-4 py-2 rounded-xl text-white font-bold text-sm"
                   style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                    سجّل الآن
                </a>
            @endauth
        </div>
    </div>
</nav>

{{-- ═══════════ Hero ═══════════ --}}
<div class="relative" style="background: linear-gradient(135deg, #0F1E3C 0%, #1E3A5F 50%, #152C4A 100%);">
    <div class="max-w-6xl mx-auto px-4 py-12 relative z-10">
        <div class="flex flex-col md:flex-row items-center gap-6">

            {{-- صورة --}}
            <div class="relative">
                <img src="{{ $craftsman->avatar_url }}"
                     alt="{{ $craftsman->full_name }}"
                     class="w-32 h-32 rounded-full border-4 border-gold-400 object-cover shadow-2xl">
                @if($craftsman->is_verified)
                    <div class="absolute -bottom-1 -left-1 w-10 h-10 rounded-full bg-blue-500 border-4 border-brand-800 flex items-center justify-center">
                        <i class="fa-solid fa-check text-white"></i>
                    </div>
                @endif
                @if($isAvailable)
                    <div class="absolute top-0 right-0 w-5 h-5 bg-emerald-400 rounded-full border-4 border-brand-800 animate-pulse"
                         title="متوفر الآن"></div>
                @endif
            </div>

            {{-- معلومات --}}
            <div class="flex-1 text-center md:text-right">
                <h1 class="text-3xl lg:text-4xl font-black text-white mb-2">
                    {{ $craftsman->full_name }}
                </h1>
                <p class="text-gold-400 text-lg font-bold mb-3">
                    <i class="fa-solid fa-wrench"></i>
                    {{ $craftsman->craftsmanProfile->category->name ?? 'حرفي' }}
                </p>

                {{-- نجوم --}}
                <div class="flex items-center justify-center md:justify-start gap-3 mb-4 flex-wrap">
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= round($stats['rating_avg']) ? 'text-gold-400' : 'text-white/20' }}"></i>
                        @endfor
                    </div>
                    <span class="text-white font-black text-xl">{{ number_format($stats['rating_avg'], 1) }}</span>
                    <span class="text-white/60 text-sm">({{ $stats['total_reviews'] }} تقييم)</span>
                </div>

                {{-- شارات --}}
                <div class="flex items-center justify-center md:justify-start gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-xl bg-white/10 text-white/90 text-xs font-bold">
                        <i class="fa-solid fa-briefcase"></i> {{ $stats['experience_years'] }} سنوات خبرة
                    </span>
                    <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                        <i class="fa-solid fa-circle-check"></i> {{ $stats['completed_jobs'] }} خدمة منجزة
                    </span>
                    @if($isAvailable)
                        <span class="px-3 py-1 rounded-xl bg-gold-500/20 text-gold-300 text-xs font-bold border border-gold-500/30">
                            <i class="fa-solid fa-circle"></i> متوفر الآن
                        </span>
                    @endif
                </div>
            </div>

            {{-- زر الطلب --}}
            <div class="flex flex-col gap-2">
                <a href="{{ route('auth.register') }}?craftsman_id={{ $craftsman->id }}"
                   class="px-6 py-4 rounded-2xl text-white font-black text-sm flex items-center gap-2 justify-center shadow-2xl hover:shadow-glow transition"
                   style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                    <i class="fa-solid fa-paper-plane"></i>
                    اطلب خدمة
                </a>
                <button onclick="navigator.share ? navigator.share({title: '{{ $craftsman->full_name }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href)"
                        class="px-6 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-sm flex items-center gap-2 justify-center transition">
                    <i class="fa-solid fa-share-nodes"></i>
                    مشاركة
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════ Main Content ═══════════ --}}
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ═══════════ Right Column: Bio + Info ═══════════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Bio --}}
            @if($craftsman->craftsmanProfile->bio)
                <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
                    <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user text-gold-500"></i>
                        نبذة عني
                    </h2>
                    <p class="text-slate-600 leading-relaxed">{{ $craftsman->craftsmanProfile->bio }}</p>
                </div>
            @endif

            {{-- معرض الأعمال --}}
            @if($portfolio->isNotEmpty())
                <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
                    <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-images text-gold-500"></i>
                        معرض الأعمال
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($portfolio as $work)
                            <div class="rounded-2xl overflow-hidden border border-slate-200 hover:shadow-lift transition">
                                <img src="{{ $work->problem_image_url }}" 
                                     class="w-full h-32 object-cover"
                                     alt="{{ $work->title }}">
                                <div class="p-3">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $work->title }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1">
                                        {{ $work->created_at->format('Y-m-d') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- التقييمات --}}
            <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
                <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-star text-gold-500"></i>
                    التقييمات ({{ $stats['total_reviews'] }})
                </h2>

                @if($reviews->isEmpty())
                    <div class="text-center py-8">
                        <i class="fa-regular fa-star text-4xl text-slate-300 mb-2"></i>
                        <p class="text-slate-500 text-sm">لا توجد تقييمات بعد</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($reviews as $review)
                            <div class="border border-slate-100 rounded-2xl p-4">
                                <div class="flex items-start gap-3 mb-2">
                                    <img src="{{ $review->client->avatar_url ?? asset('assets/images/logo.png') }}"
                                         class="w-10 h-10 rounded-full object-cover">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-800 text-sm">{{ $review->client->full_name ?? 'عميل' }}</p>
                                        <div class="flex items-center gap-1 mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star text-xs {{ $i <= $review->rating ? 'text-gold-500' : 'text-slate-200' }}"></i>
                                            @endfor
                                            <span class="text-xs text-slate-400 mr-2">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="text-sm text-slate-600 pr-13">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══════════ Left Column: Stats ═══════════ --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- بطاقة الإحصائيات --}}
            <div class="bg-gradient-to-br from-brand-800 to-brand-600 rounded-3xl p-6 text-white shadow-lift">
                <h3 class="font-black text-lg mb-5">
                    <i class="fa-solid fa-chart-simple text-gold-400"></i>
                    الإحصائيات
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-white/70 text-sm">متوسط التقييم</span>
                        <span class="font-black text-gold-400 text-lg">{{ number_format($stats['rating_avg'], 1) }} ⭐</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/70 text-sm">عدد التقييمات</span>
                        <span class="font-black">{{ $stats['total_reviews'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/70 text-sm">خدمات منجزة</span>
                        <span class="font-black">{{ $stats['completed_jobs'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/70 text-sm">سنوات الخبرة</span>
                        <span class="font-black">{{ $stats['experience_years'] }}</span>
                    </div>
                    @if($craftsman->craftsmanProfile->hourly_rate > 0)
                        <div class="flex items-center justify-between pt-3 border-t border-white/10">
                            <span class="text-white/70 text-sm">السعر/ساعة</span>
                            <span class="font-black text-gold-400">{{ number_format($craftsman->craftsmanProfile->hourly_rate) }} ر.ي</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- توزيع التقييمات --}}
            @if($stats['total_reviews'] > 0)
                <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
                    <h3 class="font-black text-slate-800 mb-4 text-sm">
                        <i class="fa-solid fa-chart-bar text-gold-500"></i>
                        توزيع التقييمات
                    </h3>
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php
                            $count = $distribution[$star];
                            $percent = $stats['total_reviews'] > 0 ? ($count / $stats['total_reviews']) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold text-slate-600 w-6">{{ $star }}★</span>
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gold-500 rounded-full transition-all"
                                     style="width: {{ $percent }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-slate-400 w-8">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- معلومات الاتصال --}}
            @auth
                @if(auth()->user()->isClient())
                    <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
                        <h3 class="font-black text-slate-800 mb-3 text-sm">
                            <i class="fa-solid fa-address-card text-gold-500"></i>
                            معلومات الاتصال
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p class="flex items-center gap-2 text-slate-600">
                                <i class="fa-solid fa-phone text-gold-500 w-4"></i>
                                <span dir="ltr">{{ $craftsman->phone }}</span>
                            </p>
                            @if($craftsman->craftsmanProfile->is_emergency && $craftsman->craftsmanProfile->emergency_phone)
                                <p class="flex items-center gap-2 text-red-600">
                                    <i class="fa-solid fa-bolt w-4"></i>
                                    <span dir="ltr">{{ $craftsman->craftsmanProfile->emergency_phone }}</span>
                                    <span class="text-xs">(طوارئ)</span>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-gold-50 rounded-3xl p-6 border border-gold-200">
                    <p class="text-sm text-gold-800 text-center">
                        <i class="fa-solid fa-lock"></i>
                        سجّل دخولك لرؤية معلومات الاتصال
                    </p>
                    <a href="{{ route('auth.login') }}"
                       class="block mt-3 py-2.5 rounded-2xl text-white font-bold text-sm text-center"
                       style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                        تسجيل الدخول
                    </a>
                </div>
            @endauth

        </div>
    </div>
</div>

{{-- ═══════════ Footer ═══════════ --}}
<footer class="bg-brand-900 text-white mt-16">
    <div class="max-w-6xl mx-auto px-4 py-8 text-center">
        <p class="text-white/60 text-sm">
            © {{ date('Y') }} {{ config('hirfati.site_name') }} — جميع الحقوق محفوظة
        </p>
    </div>
</footer>

</body>
</html>
