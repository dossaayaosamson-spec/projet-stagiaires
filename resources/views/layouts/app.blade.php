<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Découvrez AGAU Éditions, votre bibliothèque numérique dédiée aux grandes œuvres littéraires africaines. Romans, essais, nouvelles accessibles partout.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-KSL39DGW');</script>
    <!-- End Google Tag Manager -->

    <title>@yield('title', 'AGAU Éditions — Bibliothèque & Maison d\'Éditions')</title>

    <link rel="preload" as="image" href="{{ asset('images/hero/hero-1.webp') }}" fetchpriority="high">

    <link rel="shortcut icon" href="{{ asset('images/AGAU LOGO BLEU.png') }}" type="image/x-icon">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;900&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- A terminer plus tard
    <style>
        :root {
            scrollbar-color: var(--agau-blue) var(--agau-blue);
        }
    </style>
    -->
</head>
<body class="font-sans antialiased bg-agau-off-white text-agau-text m-0 p-0 box-border">
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KSL39DGW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <x-navbar />

    <main>
        @yield('content')
    </main>

    <x-footer />

    @include('partials.legal-modals')

    @stack('scripts')
    @auth
    <script>
        // Rafraîchit la page automatiquement à la fin de la session (5min pour admin/author, 10min pour lecteur)
        setTimeout(function() {
            window.location.reload();
        }, {{ in_array(auth()->user()->role, ['admin', 'author']) ? 300 * 1000 : 600 * 1000 }});
    </script>
    @endauth
</body>
</html>
