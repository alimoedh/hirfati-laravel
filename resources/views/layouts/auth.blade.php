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
    <script>
    (function() {
        const theme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>

</head>
<body class="min-h-screen font-cairo antialiased bg-brand-900 text-white overflow-x-hidden">

    {{-- Background --}}
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-900"></div>
        <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-gold-500/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-blue-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-purple-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
    </div>

    @yield('content')

    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {});
    }
    </script>
    @stack('scripts')
</body>
</html>
