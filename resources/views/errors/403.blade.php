<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('hirfati.site_name') }} | غير مصرح</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-cairo bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-3xl p-10 max-w-lg w-full text-center shadow-lift border border-slate-100">
        <div class="text-6xl text-red-500 mb-4">
            <i class="fa-solid fa-lock"></i>
        </div>

        <h1 class="text-7xl font-black text-brand-800 leading-none mb-3">403</h1>
        <h2 class="text-xl font-black text-slate-800 mb-2">غير مصرح لك بالدخول</h2>
        <p class="text-slate-500 text-sm mb-6">
            عذراً، لا تملك صلاحية الوصول إلى هذه الصفحة.
        </p>

        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-white font-bold text-sm"
           style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-house"></i> العودة للرئيسية
        </a>
    </div>

</body>
</html>
