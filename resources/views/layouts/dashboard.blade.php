<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tableau de bord — AGAU Éditions')</title>

    <link rel="shortcut icon" href="{{ asset('images/AGAU LOGO BLEU.png') }}" type="image/x-icon">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-agau-off-white text-agau-text min-h-screen selection:bg-agau-blue/10 selection:text-agau-blue">

    <x-sidebar />

    <!-- Overlay for mobile sidebar -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden lg:hidden"></div>

    <!-- Main Content -->
    <main class="lg:ml-64 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="sticky top-0 z-40 bg-agau-off-white/80 backdrop-blur-md px-6 md:px-10 py-4 flex justify-between items-center w-full border-b border-agau-border/20">
            <div class="flex items-center gap-4 flex-1">
                <button id="sidebar-toggle" class="lg:hidden flex items-center justify-center p-2 text-agau-blue bg-white border border-agau-border/40 rounded-lg cursor-pointer">
                    <i class="bi bi-list text-2xl"></i>
                </button>
                
                <div class="relative w-full max-w-md hidden sm:block">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-agau-muted/60 text-[14px]"></i>
                    <input type="text" placeholder="Rechercher dans votre bibliothèque..." class="w-full bg-agau-border/10 border-none rounded-full py-2 pl-10 pr-4 text-xs focus:ring-1 focus:ring-agau-blue/20 placeholder:text-agau-muted/40 transition-all">
                </div>
            </div>
            
            <div class="flex items-center gap-3 md:gap-6">
                <div class="flex items-center gap-4">
                    <x-notification-center />
                </div>
                
                <div class="flex items-center gap-3 pl-3 md:pl-6 border-l border-agau-border/30">
                    <div class="flex flex-col text-right hidden md:flex">
                        <span class="text-xs font-bold leading-tight">{{ auth()->user()->alias ?? auth()->user()->prenom }}</span>
                        <span class="text-[9px] text-agau-muted uppercase tracking-tighter">{{ strtolower(auth()->user()->role) === 'auteur' ? 'Auteur' : 'Lecteur' }}</span>
                    </div>
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-agau-blue text-white flex items-center justify-center font-bold text-[10px] md:text-xs shadow-sm border-2 border-white">
                        @if(auth()->user()->alias)
                            {{ strtoupper(substr(auth()->user()->alias, 0, 1)) }}
                        @else
                            {{ strtoupper(substr(auth()->user()->prenom, 0, 1) . substr(auth()->user()->nom_famille, 0, 1)) }}
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="px-6 md:px-12 py-10 flex-1">
            @yield('content')
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggle = document.getElementById('sidebar-toggle');
            const close = document.getElementById('sidebar-close');

            function toggleSidebar() {
                if (sidebar) sidebar.classList.toggle('-translate-x-full');
                if (overlay) overlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }

            if (toggle && sidebar && overlay) {
                toggle.onclick = toggleSidebar;
                overlay.onclick = toggleSidebar;
                if (close) close.onclick = toggleSidebar;
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
