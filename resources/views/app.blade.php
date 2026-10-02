<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f8fafc]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="referrer" content="no-referrer">

        @php
            $privatePage = request()->is('admin*', 'vendor*', 'account*', 'bookings*', 'support*', 'oleh-oleh/keranjang*', 'oleh-oleh/pesanan*', 'login', 'register', '*password*', 'auth/*');
            $seo = $seo ?? ['title' => config('app.name', 'TapakLokal'), 'description' => 'Temukan perjalanan, oleh-oleh, dan pengalaman lokal bersama mitra TapakLokal.', 'canonical' => url()->current(), 'robots' => $privatePage ? 'noindex,nofollow' : 'index,follow'];
        @endphp
        <title inertia>{{ $seo['title'] }} - TapakLokal</title>
        <meta name="description" content="{{ $seo['description'] }}" inertia="description">
        <meta name="robots" content="{{ $seo['robots'] }}" inertia="robots">
        <link rel="canonical" href="{{ $seo['canonical'] }}" inertia="canonical">
        <meta property="og:title" content="{{ $seo['title'] }}" inertia="og:title">
        <meta property="og:description" content="{{ $seo['description'] }}" inertia="og:description">
        <meta property="og:url" content="{{ $seo['canonical'] }}" inertia="og:url">
        <meta property="og:type" content="website" inertia="og:type">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', 'resources/css/app.css'])
        @inertiaHead
    </head>
    <body class="h-full font-sans antialiased text-[#172c50] bg-[#f8fafc]">
        @inertia
    </body>
</html>
