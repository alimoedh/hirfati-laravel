<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('hirfati.site_name') }} | الصفحة غير موجودة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-cairo bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-3xl p-10 max-w-lg w-full text-center shadow-lift border border-slate-100">
        <div class="text-6xl text-gold-500 mb-4">
            <i class="fa-regular fa-face-frown"></i>
        </div>

        <h1 class="text-7xl font-black text-brand-800 leading-none mb-3">404</h1>
        <h2 class="text-xl font-black text-slate-800 mb-2">الصفحة غير موجودة</h2>
        <p class="text-slate-500 text-sm mb-6">
            عذراً، الصفحة التي تبحث عنها قد تكون نُقلت أو حُذفت.
        </p>

        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-white font-bold text-sm"
           style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-house"></i> العودة للرئيسية
        </a>

        <hr class="border-slate-100 my-6">

        <div class="text-right space-y-2">
            <p class="font-bold text-slate-700 text-sm mb-3">قد تبحث عن:</p>

            <a href="{{ route('client.search') }}" class="flex items-center gap-2 text-brand-700 hover:text-gold-600 font-semibold text-sm py-1 transition-colors">
                <i class="fa-solid fa-search"></i> البحث عن حرفي
            </a>

            @auth
                @php
                    $dashboardUrl = match(auth()->user()->role) {
                        'admin'     => route('admin.dashboard'),
                        'craftsman' => route('craftsman.dashboard'),
                        default     => route('client.dashboard'),
                    };
                @endphp
                <a href="{{ $dashboardUrl }}" class="flex items-center gap-2 text-brand-700 hover:text-gold-600 font-semibold text-sm py-1 transition-colors">
                    <i class="fa-solid fa-chart-line"></i> لوحة التحكم
                </a>
            @else
                <a href="{{ route('auth.login') }}" class="flex items-center gap-2 text-brand-700 hover:text-gold-600 font-semibold text-sm py-1 transition-colors">
                    <i class="fa-solid fa-right-to-bracket"></i> تسجيل الدخول
                </a>
            @endauth
        </div>
    </div>

</body>
</html>
