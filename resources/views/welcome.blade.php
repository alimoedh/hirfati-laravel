<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('hirfati.site_name') }} | الرئيسية</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0F1E3C">

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ============================================
           Hero Background - Dynamic Image
           ============================================ */
        .hero-main {
            position: relative;
            min-height: 700px;
            background-image: url('https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            overflow: hidden;
        }
        .hero-main::before {
            content: '';
            position: absolute;
            inset: 0;
        }
        .hero-main::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(212,162,76,0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(59,130,246,0.15) 0%, transparent 50%);
            animation: pulse-bg 8s ease-in-out infinite;
        }
        @keyframes pulse-bg {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }

        /* ============================================
           Floating Elements
           ============================================ */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }
        .floating { animation: float 6s ease-in-out infinite; }
        .floating-slow { animation: float-slow 8s ease-in-out infinite; }

        /* ============================================
           Gradient Text
           ============================================ */
        .text-gradient-gold {
            background: linear-gradient(135deg, #FCD34D 0%, #E5B968 50%, #D4A24C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ============================================
           Craftsman Cards - 3D Hover
           ============================================ */
        .craftsman-card {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }
        .craftsman-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 30px 60px -15px rgba(15,30,60,0.3);
        }
        .craftsman-card .avatar-img {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .craftsman-card:hover .avatar-img {
            transform: scale(1.1) rotate(-3deg);
        }

        /* ============================================
           Category Cards - Animated
           ============================================ */
        .category-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .category-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(212,162,76,0.1), transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }
        .category-card:hover::before { opacity: 1; }
        .category-card:hover {
            transform: translateY(-8px);
            border-color: #D4A24C;
            box-shadow: 0 20px 40px -10px rgba(212,162,76,0.3);
        }

        /* ============================================
           Bento Stats
           ============================================ */
        .bento-item {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .bento-item:hover {
            transform: translateY(-6px) scale(1.02);
        }

        /* ============================================
           Section Entrance
           ============================================ */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ============================================
           Search Input
           ============================================ */
        .search-hero {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(20px);
            border: 1.5px solid rgba(255,255,255,0.25);
            transition: all 0.3s;
        }
        .search-hero:focus-within {
            border-color: rgba(212,162,76,0.6);
            background: rgba(255,255,255,0.18);
            box-shadow: 0 0 40px rgba(212,162,76,0.3);
        }

        /* ============================================
           Navbar Glass
           ============================================ */
        .glass-nav {
            background: rgba(15,30,60,0.75);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
    </style>
    
</head>
<body class="font-cairo bg-slate-50">

{{-- ============================================
     NAVBAR
     ============================================ --}}
<nav class="glass-nav sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-3 flex items-center justify-between">

        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('hirfati.site_name') }}"
                 class="h-14 w-auto">
        </a>

        <div class="hidden lg:flex items-center gap-8">
            <a href="{{ url('/') }}" class="text-white/80 hover:text-gold-400 font-semibold text-sm transition-colors">الرئيسية</a>
            <a href="{{ route('client.search') }}" class="text-white/80 hover:text-gold-400 font-semibold text-sm transition-colors">الحرفيون</a>
            <a href="#features" class="text-white/80 hover:text-gold-400 font-semibold text-sm transition-colors">المزايا</a>
            <a href="#" class="text-white/80 hover:text-gold-400 font-semibold text-sm transition-colors">تواصل معنا</a>
        </div>

        <div class="flex items-center gap-3">
            @auth
                @php
                    $dashboardUrl = match(auth()->user()->role) {
                        'admin'     => route('admin.dashboard'),
                        'craftsman' => route('craftsman.dashboard'),
                        default     => route('client.dashboard'),
                    };
                @endphp
                <a href="{{ $dashboardUrl }}"
                   class="hidden lg:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gold-500 hover:bg-gold-600 text-white font-bold text-sm">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>لوحة التحكم</span>
                </a>
                <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-500/20 hover:bg-red-500/30 text-red-300 font-bold text-sm">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            @else
                <a href="{{ route('auth.login') }}"
                   class="hidden lg:inline-flex px-4 py-2 rounded-xl text-white/80 hover:text-gold-400 font-bold text-sm">
                    تسجيل الدخول
                </a>
                <a href="{{ route('auth.register') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gold-500 hover:bg-gold-600 text-white font-bold text-sm shadow-glow">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>انضم الآن</span>
                </a>
            @endauth
        </div>
    </div>
</nav>

{{-- ============================================
     HERO SECTION - ضخم مع صورة خلفية
     ============================================ --}}
<section class="hero-main text-white relative">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-20 lg:py-28 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            {{-- Left Content --}}
            <div class="space-y-8">

                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-2 backdrop-blur">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold">منصة موثوقة الأولى في اليمن</span>
                </div>

                {{-- Title --}}
                <h1 class="text-4xl lg:text-7xl font-black leading-tight">
                    ابحث عن أفضل
                    <span class="block mt-2 text-gradient-gold">الحرفيين</span>
                    <span class="text-2xl lg:text-4xl text-white/90 font-bold block mt-3">
                        في منطقتك بسهولة وأمان
                    </span>
                </h1>

                {{-- Subtitle --}}
                <p class="text-white/70 text-lg leading-relaxed max-w-xl">
                    منصة حرفتي تربطك بأفضل الحرفيين الموثوقين والمعتمدين لإنجاز أعمالك بكل احترافية وسرعة.
                </p>

                {{-- Search --}}
                <form action="{{ route('client.search') }}" method="GET"
                      class="search-hero rounded-2xl p-2 flex gap-2 max-w-xl">
                    <input type="text" name="q"
                           placeholder="🔍 ابحث عن حرفي... (سباك، كهربائي، نجار)"
                           required
                           class="flex-1 bg-transparent border-0 outline-none px-4 py-3 text-white placeholder-white/50 text-sm font-cairo">
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-br from-gold-400 to-gold-600 text-white font-bold rounded-xl hover:shadow-glow-lg transition-all">
                        <i class="fa-solid fa-search"></i>
                        <span class="hidden sm:inline mr-2">ابحث</span>
                    </button>
                </form>

                {{-- Trust Stats --}}
                <div class="flex flex-wrap gap-8 pt-4">
                    @foreach([
                        ['fa-user-check', $totalCraftsmen . '+', 'حرفي معتمد'],
                        ['fa-circle-check', $totalRequests . '+', 'خدمة منجزة'],
                        ['fa-star', '4.8', 'متوسط التقييم'],
                    ] as $s)
                        <div class="flex items-center gap-3">
                            <i class="fa-solid {{ $s[0] }} text-gold-400 text-2xl"></i>
                            <div>
                                <p class="text-2xl font-black">{{ $s[1] }}</p>
                                <p class="text-xs text-white/60">{{ $s[2] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right Illustration - Floating Cards --}}
            <div class="hidden lg:block relative">
                <div class="relative w-full aspect-square max-w-lg mx-auto">

                    {{-- Glow --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-gold-400/30 to-blue-500/10 rounded-full blur-3xl animate-pulse"></div>

                    {{-- Craftsman Card 1 --}}
                    <div class="absolute top-8 right-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-4 w-64 floating shadow-2xl">
                        <div class="flex items-center gap-3">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg"
                                 class="w-14 h-14 rounded-full border-2 border-gold-400 object-cover">
                            <div>
                                <p class="text-sm font-bold">علي السباك</p>
                                <p class="text-xs text-gold-400">⭐ 4.9 · سباكة</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-white/10 flex items-center justify-between">
                            <span class="text-xs text-emerald-400"><i class="fa-solid fa-circle"></i> متوفر</span>
                            <span class="text-xs text-white/60">5 سنوات خبرة</span>
                        </div>
                    </div>

                    {{-- Completed Order --}}
                    <div class="absolute top-1/3 left-0 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-4 w-52 floating-slow shadow-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-check text-emerald-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-white/60">الطلب</p>
                                <p class="text-sm font-bold">مكتمل ✅</p>
                            </div>
                        </div>
                    </div>

                    {{-- Wallet Balance --}}
                    <div class="absolute bottom-16 right-8 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-5 w-56 floating shadow-2xl" style="animation-delay: 2s;">
                        <p class="text-xs text-white/60 mb-1">💰 رصيدك</p>
                        <p class="text-2xl font-black text-gradient-gold">12,500 ر.ي</p>
                        <div class="mt-2 h-1.5 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full w-3/4 bg-gradient-to-r from-gold-400 to-gold-600 rounded-full"></div>
                        </div>
                    </div>

                    {{-- New Request --}}
                    <div class="absolute bottom-0 left-12 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-4 w-48 floating-slow shadow-2xl" style="animation-delay: 4s;">
                        <p class="text-xs text-white/60 mb-1">🔔 إشعار جديد</p>
                        <p class="text-xs font-bold">طلب خدمة جديد</p>
                        <p class="text-[10px] text-gold-400 mt-1">منذ دقيقتين</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================
     CATEGORIES
     ============================================ --}}
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-16 fade-in-up">
    <div class="flex items-end justify-between mb-8">
        <div>
            <h2 class="text-3xl lg:text-4xl font-black text-slate-800 mb-2">
                <i class="fa-solid fa-tags text-gold-500"></i> التخصصات
            </h2>
            <p class="text-slate-500">اختر التخصص الذي تحتاجه</p>
        </div>
        <a href="{{ route('client.search') }}"
           class="hidden lg:flex items-center gap-2 text-gold-600 font-bold hover:gap-3 transition-all">
            عرض الكل <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        @foreach($categories as $cat)
            @php
                $images = [
                    1 => 'https://images.unsplash.com/photo-1581579438747-1dc8d17bbce4?w=400&q=80',
                    2 => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=400&q=80',
                    3 => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?w=400&q=80',
                    4 => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=400&q=80',
5 => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=400&q=80',                    6 => 'https://images.unsplash.com/photo-1533240332313-0db49b459ad6?w=400&q=80',
                ];
                $bg = $images[$cat->id] ?? $images[1];
            @endphp
            <a href="{{ route('client.search', ['category' => $cat->id]) }}"
               class="category-card rounded-3xl overflow-hidden bg-white border-2 border-slate-100 relative group">
                <div class="h-24 bg-cover bg-center relative" style="background-image: url('{{ $bg }}')">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-900/80 to-transparent"></div>
                    <div class="absolute bottom-2 right-2 w-10 h-10 rounded-xl bg-gold-500 flex items-center justify-center">
                        <i class="fa-solid {{ $cat->icon }} text-white text-lg"></i>
                    </div>
                </div>
                <div class="p-3 text-center">
                    <p class="font-bold text-slate-800 text-sm">{{ $cat->name }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- ============================================
     BENTO STATS
     ============================================ --}}
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-16 fade-in-up">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bento-item bg-gradient-to-br from-brand-800 to-brand-700 rounded-3xl p-6 text-white col-span-2 lg:col-span-1">
            <i class="fa-solid fa-users text-3xl text-gold-400 mb-4"></i>
            <p class="text-4xl lg:text-5xl font-black" x-data="counter({{ $totalUsers ?? 23 }})">
                <span x-text="formatted"></span>
            </p>
            <p class="text-sm text-white/70 mt-2">مستخدم</p>
        </div>

        <div class="bento-item bg-gradient-to-br from-gold-500 to-gold-600 rounded-3xl p-6 text-white">
            <i class="fa-solid fa-user-gear text-3xl mb-4"></i>
            <p class="text-4xl lg:text-5xl font-black" x-data="counter({{ $totalCraftsmen }})">
                <span x-text="formatted"></span>
            </p>
            <p class="text-sm text-white/80 mt-2">حرفي</p>
        </div>

        <div class="bento-item bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-3xl p-6 text-white">
            <i class="fa-solid fa-circle-check text-3xl mb-4"></i>
            <p class="text-4xl lg:text-5xl font-black" x-data="counter({{ $totalRequests }})">
                <span x-text="formatted"></span>
            </p>
            <p class="text-sm text-white/80 mt-2">خدمة منجزة</p>
        </div>

        <div class="bento-item bg-gradient-to-br from-blue-500 to-blue-700 rounded-3xl p-6 text-white">
            <i class="fa-solid fa-star text-3xl mb-4"></i>
            <p class="text-4xl lg:text-5xl font-black">4.8</p>
            <p class="text-sm text-white/80 mt-2">متوسط التقييم</p>
        </div>
    </div>
</section>

{{-- ============================================
     FEATURED CRAFTSMEN - صور حقيقية
     ============================================ --}}
@if($featuredCraftsmen->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-16 fade-in-up">
    <div class="flex items-end justify-between mb-8">
        <div>
            <h2 class="text-3xl lg:text-4xl font-black text-slate-800 mb-2">
                <i class="fa-solid fa-star text-gold-500"></i> حرفيون مميزون
            </h2>
            <p class="text-slate-500">الأعلى تقييماً على المنصة</p>
        </div>
        <a href="{{ route('client.search') }}"
           class="hidden lg:flex items-center gap-2 text-gold-600 font-bold hover:gap-3 transition-all">
            عرض الكل <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @php
            $craftsmanImages = [
                'https://randomuser.me/api/portraits/men/32.jpg',
                'https://randomuser.me/api/portraits/men/45.jpg',
                'https://randomuser.me/api/portraits/men/67.jpg',
                'https://randomuser.me/api/portraits/men/12.jpg',
                'https://randomuser.me/api/portraits/men/78.jpg',
                'https://randomuser.me/api/portraits/men/23.jpg',
                'https://randomuser.me/api/portraits/men/91.jpg',
                'https://randomuser.me/api/portraits/men/55.jpg',
            ];
        @endphp
        @foreach($featuredCraftsmen as $i => $c)
            @php
                $profile = $c->craftsmanProfile;
                $avatar = $craftsmanImages[$i % count($craftsmanImages)];
            @endphp
            <div class="craftsman-card bg-white rounded-3xl overflow-hidden shadow-soft border border-slate-100">
                {{-- Header with Image --}}
                <div class="h-32 relative bg-gradient-to-br from-brand-800 to-brand-600">
                    <div class="absolute -bottom-12 right-1/2 translate-x-1/2">
                        <div class="relative">
                            <img src="{{ $avatar }}"
                                 alt="{{ $c->full_name }}"
                                 class="avatar-img w-28 h-28 rounded-full border-4 border-white object-cover shadow-2xl">
                            @if($profile->is_approved)
                                <div class="absolute bottom-1 left-1 w-8 h-8 rounded-full bg-blue-500 border-3 border-white flex items-center justify-center">
                                    <i class="fa-solid fa-check text-white text-xs"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-16 pb-6 px-6 text-center">
                    <h3 class="font-bold text-slate-800 text-lg mb-1">{{ $c->full_name }}</h3>
                    <p class="text-sm text-slate-500 mb-3">{{ $profile->category->name ?? 'غير محدد' }}</p>

                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="flex items-center gap-1 bg-amber-100 px-3 py-1 rounded-xl">
                            <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                            <span class="text-xs font-bold text-amber-700">{{ number_format($profile->rating_avg ?? 0, 1) }}</span>
                        </div>
                        <span class="text-xs text-slate-400">·</span>
                        <span class="text-xs text-slate-500">{{ $profile->experience_years ?? 0 }} سنوات خبرة</span>
                    </div>

                    @if($profile->is_available)
                        <div class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-3 py-1 rounded-xl text-xs font-bold mb-4">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            متوفر الآن
                        </div>
                    @endif

                    <a href="{{ route('client.request-form') }}?craftsman_id={{ $c->id }}"
                       class="block w-full py-3 rounded-2xl bg-gradient-to-br from-gold-400 to-gold-600 text-white font-bold text-sm hover:shadow-glow transition-all">
                        <i class="fa-solid fa-paper-plane ml-1"></i>
                        طلب الخدمة
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- ============================================
     FEATURES
     ============================================ --}}
<section id="features" class="max-w-7xl mx-auto px-4 lg:px-6 py-16 fade-in-up">
    <div class="text-center mb-12">
        <h2 class="text-3xl lg:text-4xl font-black text-slate-800 mb-3">لماذا تختار حرفتي؟</h2>
        <p class="text-slate-500 max-w-2xl mx-auto">نقدم لك تجربة استخدام كاملة وآمنة</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['fa-shield-halved', 'دفع آمن', 'المبلغ محجوز في الأمانة حتى إكمال الخدمة', 'blue'],
            ['fa-user-check', 'حرفيون موثقون', 'جميع الحرفيين معتمدون بعد التحقق', 'emerald'],
            ['fa-clock', 'ضمان 30 يوم', 'ضمان شامل على جميع الخدمات', 'amber'],
            ['fa-headset', 'دعم 24/7', 'فريق دعم متواجد لمساعدتك', 'purple'],
        ] as $f)
            <div class="bg-white rounded-3xl p-6 shadow-soft hover:shadow-lift transition-all hover:-translate-y-2">
                <div class="w-14 h-14 rounded-2xl bg-{{ $f[3] }}-100 flex items-center justify-center mb-4">
                    <i class="fa-solid {{ $f[0] }} text-{{ $f[3] }}-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-slate-800 mb-2">{{ $f[1] }}</h3>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $f[2] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============================================
     CTA
     ============================================ --}}
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-16 fade-in-up">
    <div class="relative overflow-hidden rounded-3xl p-12 lg:p-16 text-center"
         style="background: linear-gradient(135deg, #0F1E3C 0%, #1E3A5F 100%);">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 right-0 w-96 h-96 bg-gold-500 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-2xl mx-auto">
            <h2 class="text-3xl lg:text-5xl font-black text-white mb-4">جاهز تبدأ؟</h2>
            <p class="text-white/70 text-lg mb-8">انضم إلى آلاف المستخدمين الذين يثقون بحرفتي</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('auth.register') }}"
                   class="px-8 py-4 rounded-2xl bg-gold-500 hover:bg-gold-600 text-white font-bold transition-all hover:shadow-glow-lg hover:-translate-y-1">
                    <i class="fa-solid fa-user-plus ml-2"></i>
                    إنشاء حساب جديد
                </a>
                <a href="{{ route('client.search') }}"
                   class="px-8 py-4 rounded-2xl bg-white/10 backdrop-blur border border-white/20 text-white font-bold hover:bg-white/20 transition-all">
                    <i class="fa-solid fa-search ml-2"></i>
                    تصفح الحرفيين
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ============================================
     FOOTER
     ============================================ --}}
<footer class="bg-brand-900 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 lg:px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('hirfati.site_name') }}" class="w-32 mb-4">
                <p class="text-white/60 text-sm leading-relaxed">
                    منصة تربط العملاء بأفضل الحرفيين الموثوقين في اليمن بكل سهولة وأمان.
                </p>
            </div>
            <div>
                <h4 class="font-bold text-gold-400 mb-4">روابط سريعة</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="text-white/70 hover:text-gold-400">الرئيسية</a></li>
                    <li><a href="{{ route('client.search') }}" class="text-white/70 hover:text-gold-400">البحث عن حرفي</a></li>
                    <li><a href="{{ route('auth.register') }}" class="text-white/70 hover:text-gold-400">انضم إلينا</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-gold-400 mb-4">تواصل معنا</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="tel:+967774434096" class="flex items-center gap-2 text-white/70 hover:text-gold-400">
                        <i class="fa-solid fa-phone"></i> <span dir="ltr">+967 774 434 096</span></a></li>
                    <li><a href="mailto:Alimoedh42@gmail.com" class="flex items-center gap-2 text-white/70 hover:text-gold-400">
                        <i class="fa-solid fa-envelope"></i> Alimoedh42@gmail.com</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-gold-400 mb-4">ضماناتنا</h4>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2 text-white/70"><i class="fa-solid fa-shield-halved text-emerald-400"></i> ضمان 30 يوم</li>
                    <li class="flex items-center gap-2 text-white/70"><i class="fa-solid fa-lock text-emerald-400"></i> دفع آمن</li>
                    <li class="flex items-center gap-2 text-white/70"><i class="fa-solid fa-user-check text-emerald-400"></i> حرفيين موثقين</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 mt-8 pt-6 text-center">
            <p class="text-xs text-white/50">© {{ date('Y') }} {{ config('hirfati.site_name') }} — جميع الحقوق محفوظة</p>
        </div>
    </div>
</footer>

<script>
// Fade in on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
    });
}, { threshold: 0.1 });
document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {});
}
</script>

</body>
</html>
