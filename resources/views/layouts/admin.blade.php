<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('hirfati.site_name') }} | @yield('title')</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-cairo bg-slate-50 min-h-screen" x-data="{ sidebarOpen: false }">

<!-- ============================================
     Mobile Overlay
     ============================================ -->
<div x-show="sidebarOpen"
     x-transition.opacity
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-brand-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

<div class="flex min-h-screen">

    <!-- ============================================
         SIDEBAR
         ============================================ -->
    <aside class="fixed lg:sticky top-0 right-0 h-screen w-72 bg-brand-800 text-white flex flex-col z-50 transition-transform duration-300 lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">

        <!-- Brand -->
        <div class="px-6 py-6 border-b border-white/[0.06]">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center">
                <img src="{{ asset('assets/images/logo.png') }}"
                     alt="{{ config('hirfati.site_name') }}"
                     class="w-36 h-auto">
            </a>
            <p class="text-center text-xs text-white/40 mt-2 font-semibold">لوحة الإدارة</p>
        </div>

        <!-- Menu -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 scrollbar-hide">

            <p class="text-[10px] text-white/30 font-bold tracking-wider px-3 mb-2">الرئيسية</p>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                <span>لوحة التحكم</span>
            </a>

            <p class="text-[10px] text-white/30 font-bold tracking-wider px-3 mt-5 mb-2">الإدارة</p>

            <a href="{{ route('admin.users.index') }}"
               class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users w-5 text-center"></i>
                <span>إدارة المستخدمين</span>
            </a>

            <a href="{{ route('admin.craftsmen.index') }}"
               class="sidebar-item {{ request()->routeIs('admin.craftsmen.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-check w-5 text-center"></i>
                <span>التحقق من الحرفيين</span>
                @if(($pendingCraftsmenCount ?? 0) > 0)
                    <span class="mr-auto bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">
                        {{ $pendingCraftsmenCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.complaints.index') }}"
               class="sidebar-item {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
                <i class="fa-solid fa-comment-dots w-5 text-center"></i>
                <span>الشكاوى</span>
                @if(($pendingComplaintsCount ?? 0) > 0)
                    <span class="mr-auto bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">
                        {{ $pendingComplaintsCount }}
                    </span>
                @endif
            </a>

            <p class="text-[10px] text-white/30 font-bold tracking-wider px-3 mt-5 mb-2">النظام</p>

            <a href="{{ route('admin.settings.index') }}"
               class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear w-5 text-center"></i>
                <span>الإعدادات</span>
            </a>

            <a href="{{ route('admin.backup.index') }}"
               class="sidebar-item {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
                <i class="fa-solid fa-cloud-arrow-up w-5 text-center"></i>
                <span>النسخ الاحتياطي</span>
            </a>
                        <a href="{{ route('admin.reports.index') }}"
               class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line w-5 text-center"></i>
                <span>التقارير المالية</span>
            </a>

        </nav>

        <!-- Logout -->
        <div class="p-3 border-t border-white/[0.06]">
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit"
                        class="sidebar-item w-full text-red-400 hover:bg-red-500/10 hover:text-red-300">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ============================================
         MAIN CONTENT
         ============================================ -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top Header -->
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-slate-200/60">
            <div class="px-4 lg:px-8 py-4 flex items-center justify-between gap-4">

                <!-- Mobile Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <!-- Welcome Message -->
                <div class="flex-1 min-w-0">
                    <h1 class="text-lg lg:text-xl font-bold text-slate-800 truncate">
                        أهلاً {{ auth()->user()->full_name }} 👋
                    </h1>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2 lg:gap-3">

                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()"
                            class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                        <i class="fa-solid fa-moon"></i>
                    </button>

                    <!-- Notifications -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 relative">
                            <i class="fa-solid fa-bell"></i>
                            @php
                                $unreadNotifs = \App\Models\Notification::where('user_id', auth()->id())
                                    ->where('is_read', false)->count();
                            @endphp
                            @if($unreadNotifs > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center pulse-gold">
                                    {{ $unreadNotifs }}
                                </span>
                            @endif
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open"
                             x-transition
                             @click.away="open = false"
                             class="absolute left-0 mt-2 w-80 bg-white rounded-2xl shadow-lift border border-slate-200 overflow-hidden z-50">
                            <div class="p-4 border-b border-slate-100">
                                <h3 class="font-bold text-slate-800">الإشعارات</h3>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @php
                                    $notifs = \App\Models\Notification::where('user_id', auth()->id())
                                        ->latest()->limit(5)->get();
                                @endphp
                                @forelse($notifs as $n)
                                    <div class="p-3 border-b border-slate-50 hover:bg-slate-50">
                                        <p class="text-sm font-bold text-slate-800">{{ $n->title }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $n->message }}</p>
                                        <p class="text-[10px] text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <p class="p-6 text-center text-sm text-slate-400">لا توجد إشعارات</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Avatar -->
                    <div class="flex items-center gap-2 pl-3 pr-2 border-r border-slate-200">
                        <div class="text-left hidden lg:block">
                            <p class="text-sm font-bold text-slate-800">{{ auth()->user()->full_name }}</p>
                            <p class="text-[10px] text-slate-500">مدير المنصة</p>
                        </div>
                        <img src="{{ auth()->user()->avatar_url }}"
                             alt="{{ auth()->user()->full_name }}"
                             class="w-10 h-10 rounded-full border-2 border-gold-400 object-cover">
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 lg:p-8">

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-start gap-3 animate-fade-in">
                    <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                    <p class="text-emerald-800 text-sm font-semibold">{!! session('success') !!}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3 animate-fade-in">
                    <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
                    <p class="text-red-800 text-sm font-semibold">{!! session('error') !!}</p>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="px-8 py-4 border-t border-slate-200/60 text-center">
            <p class="text-xs text-slate-400">
                © {{ date('Y') }} {{ config('hirfati.site_name') }} — جميع الحقوق محفوظة
            </p>
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
