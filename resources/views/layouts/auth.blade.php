<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Authentification — AGAU Éditions')</title>

    <link rel="shortcut icon" href="{{ asset('images/AGAU LOGO BLEU.png') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;900&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-agau-white text-agau-text m-0 p-0 box-border">
    <!-- Navbar Minimaliste -->
    <nav class="bg-white border-b border-agau-border px-[6%] h-[72px] flex items-center justify-between sticky top-0 z-[100]">
        <a href="{{ route('home') }}" class="no-underline">
            <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" style="width: 80px" alt="AGAU Éditions">
            <p class="text-[10px] text-agau-muted font-bold mt-1 uppercase tracking-[2px]">Maison d'éditions</p>
        </a>
        
        <ul class="hidden md:flex gap-[22px] list-none m-0 p-0">
            <li><a href="{{ route('home') }}" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Accueil</a></li>
            <li><a href="#" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Nos Livres</a></li>
            <li><a href="#" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Short Stories</a></li>
            <li><a href="#" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Nos Offres</a></li>
            <li><a href="#" class="text-agau-muted no-underline text-[0.8rem] font-semibold tracking-[1.5px] uppercase transition-colors duration-200 hover:text-agau-blue">Nos Auteurs</a></li>
        </ul>
        
        <div class="flex gap-[10px] items-center">
            <a href="{{ route('login') }}" class="border-[1.5px] {{ request()->routeIs('login') ? 'bg-agau-blue text-white' : 'bg-transparent text-agau-blue border-agau-blue' }} px-[20px] py-[9px] text-[0.7rem] font-bold tracking-[1.5px] uppercase cursor-pointer rounded-[2px] transition-all duration-200 whitespace-nowrap hover:bg-agau-blue hover:text-white no-underline text-center">Connexion</a>
            <a href="{{ route('register') }}" class="border-[1.5px] border-agau-blue {{ request()->routeIs('register') ? 'bg-agau-blue text-white' : 'bg-transparent text-agau-blue' }} px-[20px] py-[9px] text-[0.7rem] font-bold tracking-[1.5px] uppercase cursor-pointer rounded-[2px] transition-all duration-200 whitespace-nowrap hover:bg-agau-blue hover:text-white no-underline text-center">Inscription</a>
        </div>
    </nav>

    <div class="grid grid-cols-1 min-[900px]:grid-cols-2 min-h-[calc(100vh-72px)]">
        <!-- ══ LEFT PANEL ══ -->
        <div class="bg-agau-blue-deep px-[32px] py-[48px] min-[900px]:px-[52px] min-[900px]:py-[64px] flex flex-col justify-center relative overflow-hidden min-h-auto min-[900px]:min-h-full">
            <div class="absolute -top-[100px] -right-[80px] w-[380px] h-[380px] rounded-full bg-[#2B8ACF]/10"></div>
            <div class="absolute -bottom-[80px] -left-[60px] w-[280px] h-[280px] rounded-full bg-[#2B8ACF]/10"></div>
            
            @yield('left-panel')
        </div>

        <!-- ══ RIGHT PANEL ══ -->
        <div class="bg-agau-white px-[20px] py-[32px] min-[480px]:px-[32px] min-[480px]:py-[48px] min-[900px]:px-[64px] min-[900px]:py-[56px] flex flex-col justify-center overflow-y-auto">
            @yield('right-panel')
        </div>
    </div>

    @include('partials.legal-modals')
    @if(in_array(request()->route()?->getName(), ['login', 'register'], true))
        @include('components.catalogue-assistant')
    @endif
    @stack('scripts')
</body>
</html>
