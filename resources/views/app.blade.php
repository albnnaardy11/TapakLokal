<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f8fafc]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="referrer" content="no-referrer">
        <meta name="theme-color" content="#3E7BEF">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="TapakLokal">

        @php
            $privatePage = request()->is('admin*', 'vendor*', 'account*', 'bookings*', 'support*', 'oleh-oleh/keranjang*', 'oleh-oleh/pesanan*', 'login', 'register', '*password*', 'auth/*');
            $seo = $seo ?? [
                'title' => config('app.name', 'TapakLokal') . ' - Jelajahi Indonesia Secara Otentik',
                'description' => 'Platform Open Trip, Private Trip terpercaya, dan Open PO Oleh-Oleh khas nusantara langsung dari mitra lokal terverifikasi di seluruh Indonesia.',
                'canonical' => url()->current(),
                'robots' => $privatePage ? 'noindex,nofollow' : 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1'
            ];
            $siteLogo = asset('Assets/Images/logo.webp');
        @endphp
        <title inertia>{{ $seo['title'] }}</title>
        <meta name="description" content="{{ $seo['description'] }}" inertia="description">
        <meta name="robots" content="{{ $seo['robots'] }}" inertia="robots">
        <link rel="canonical" href="{{ $seo['canonical'] }}" inertia="canonical">

        <!-- Open Graph / Facebook -->
        <meta property="og:site_name" content="TapakLokal">
        <meta property="og:title" content="{{ $seo['title'] }}" inertia="og:title">
        <meta property="og:description" content="{{ $seo['description'] }}" inertia="og:description">
        <meta property="og:url" content="{{ $seo['canonical'] }}" inertia="og:url">
        <meta property="og:type" content="website" inertia="og:type">
        <meta property="og:image" content="{{ $siteLogo }}" inertia="og:image">
        <meta property="og:locale" content="id_ID">

        <!-- Twitter Cards -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seo['title'] }}" inertia="twitter:title">
        <meta name="twitter:description" content="{{ $seo['description'] }}" inertia="twitter:description">
        <meta name="twitter:image" content="{{ $siteLogo }}" inertia="twitter:image">

        @php
            $schemaData = [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'Organization',
                        '@id' => url('/') . '/#organization',
                        'name' => 'TapakLokal',
                        'url' => url('/'),
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => $siteLogo,
                            'caption' => 'TapakLokal Logo',
                        ],
                        'description' => 'Platform Open Trip, Private Trip, dan Open PO Oleh-Oleh Otentik Nusantara',
                    ],
                    [
                        '@type' => 'WebSite',
                        '@id' => url('/') . '/#website',
                        'url' => url('/'),
                        'name' => 'TapakLokal',
                        'publisher' => [
                            '@id' => url('/') . '/#organization',
                        ],
                        'potentialAction' => [
                            '@type' => 'SearchAction',
                            'target' => [
                                '@type' => 'EntryPoint',
                                'urlTemplate' => url('/cari-trip') . '?q={search_term_string}',
                            ],
                            'query-input' => 'required name=search_term_string',
                        ],
                    ],
                ],
            ];
        @endphp
        <!-- Structured Data JSON-LD -->
        <script type="application/ld+json">
        {!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>

        @if (($page['component'] ?? null) === 'Welcome')
            @php
                $heroSection = collect($page['props']['cmsSections'] ?? [])->firstWhere('slug', 'hero');
                $heroImage = $heroSection['image_url'] ?? 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1920&q=88';
            @endphp
            <link rel="preload" as="image" href="{{ $heroImage }}" fetchpriority="high">
            <link rel="modulepreload" href="{{ Vite::asset('resources/js/Pages/Welcome.vue') }}">
        @endif

        <!-- Fonts Preconnect -->
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <link rel="dns-prefetch" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&amp;display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', 'resources/css/app.css'])
        @inertiaHead
    </head>
    <body class="h-full font-sans antialiased text-[#172c50] bg-[#f8fafc]">
        @inertia
    </body>
</html>
