<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration — AGAU Éditions')</title>
    <link rel="shortcut icon" href="{{ asset('images/AGAU LOGO BLEU.png') }}" type="image/x-icon">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        body { font-family: 'Montserrat', sans-serif; }
        .font-lora { font-family: 'Lora', serif; }
    </style>
</head>
<body class="bg-agau-off-white text-agau-text min-h-screen selection:bg-agau-blue/10 selection:text-agau-blue">

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-agau-off-white border-r border-agau-border/40 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full px-6 py-5">
            <div class="mb-4 px-2 flex justify-between items-center">
                <a href="{{ route('home') }}" class="no-underline">
                    <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" style="width: 80px" alt="AGAU Éditions">
                    <p class="text-[10px] text-agau-muted font-bold mt-1 uppercase tracking-[2px]">Maison d'éditions</p>
                </a>
                <button id="sidebar-close" class="lg:hidden text-2xl text-agau-muted bg-transparent border-none cursor-pointer">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            
            <nav class="flex-1 flex flex-col gap-1 overflow-y-auto pr-2 custom-scrollbar">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-columns-gap" style="font-size: 1.1rem;"></i>
                    <span>Tableau de bord</span>
                </a>
                
                <div class="py-2">
                    <div class="h-[1px] bg-agau-border/30 mx-4"></div>
                </div>

                <a href="{{ route('admin.books.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.books.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-book-half" style="font-size: 1.1rem;"></i>
                    <span>Bibliothèque</span>
                </a>
                
                <a href="{{ route('admin.authors.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.authors.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-people" style="font-size: 1.1rem;"></i>
                    <span>Auteurs</span>
                </a>
                
                <a href="{{ route('admin.stories.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.stories.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-journal" style="font-size: 1.1rem;"></i>
                    <span>Short Stories</span>
                </a>

                <a href="{{ route('admin.slides.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.slides.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-images" style="font-size: 1.1rem;"></i>
                    <span>Carrousel</span>
                </a>

                <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.events.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-calendar-event" style="font-size: 1.1rem;"></i>
                    <span>Événements</span>
                </a>

                <a href="{{ route('admin.candidatures.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.candidatures.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-file-earmark-person" style="font-size: 1.1rem;"></i>
                    <span>Candidatures ECLA</span>
                </a>

                <a href="{{ route('admin.manuscripts.index', ['source' => 'candidature_auteur']) }}" class="flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request('source') === 'candidature_auteur' ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-star-fill text-amber-500" style="font-size: 1rem;"></i>
                        <span>Candidatures Auteurs</span>
                    </div>
                    @php
                        $pendingCount = \App\Models\Manuscript::where('submission_source', 'candidature_auteur')->where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.manuscripts.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.manuscripts.*') && request('source') !== 'candidature_auteur' ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-journal-text" style="font-size: 1.1rem;"></i>
                    <span>Tous les manuscrits</span>
                </a>

                <a href="{{ route('admin.contracts.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.contracts.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-file-earmark-check" style="font-size: 1.1rem;"></i>
                    <span>Contrats & Droits</span>
                </a>

                <a href="{{ route('admin.payouts.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.payouts.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-cash-stack" style="font-size: 1.1rem;"></i>
                    <span>Versements Auteurs</span>
                </a>
                
                <div class="py-2">
                    <div class="h-[1px] bg-agau-border/30 mx-4"></div>
                </div>
                
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-bag" style="font-size: 1.1rem;"></i>
                    <span>Commandes</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-people" style="font-size: 1.1rem;"></i>
                    <span>Communauté</span>
                </a>

                <a href="{{ route('admin.moderation.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('admin.moderation.*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-shield-lock" style="font-size: 1.1rem;"></i>
                    <span>Modération Blog</span>
                </a>
                
                <div class="mt-2 px-2">
                    <a href="{{ route('admin.books.create') }}" class="w-full bg-agau-blue text-white py-2.5 px-4 rounded-lg text-xs font-bold shadow-sm flex items-center justify-center gap-2 hover:bg-agau-blue-dark transition-all no-underline">
                        <i class="bi bi-plus-lg" style="font-size: 1.1rem;"></i>
                        NOUVELLE ENTRÉE
                    </a>
                </div>
            </nav>

            <div class="mt-2 flex flex-col gap-1 border-t border-agau-border/30 pt-2">
                <a href="#" class="flex items-center gap-3 text-agau-muted px-4 py-2 hover:text-agau-blue hover:bg-agau-blue/5 rounded-lg transition-all text-sm font-medium">
                    <i class="bi bi-gear" style="font-size: 1.1rem;"></i>
                    <span>Paramètres</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 text-agau-muted px-4 py-2 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all text-sm font-medium border-none bg-transparent cursor-pointer text-left">
                        <i class="bi bi-box-arrow-right" style="font-size: 1.1rem;"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

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
                    <input type="text" placeholder="Rechercher..." class="w-full bg-agau-border/10 border-none rounded-full py-2 pl-10 pr-4 text-xs focus:ring-1 focus:ring-agau-blue/20 placeholder:text-agau-muted/40 transition-all">
                </div>
            </div>
            
            <div class="flex items-center gap-3 md:gap-6">
                <div class="flex items-center gap-4 hidden xs:flex">
                    <x-notification-center />
                </div>
                
                <div class="flex items-center gap-3 pl-3 md:pl-6 border-l border-agau-border/30">
                    <div class="flex flex-col text-right hidden md:flex">
                        <span class="text-xs font-bold leading-tight">{{ auth()->user()->prenom }}</span>
                        <span class="text-[9px] text-agau-muted uppercase tracking-tighter">@yield('role-name', 'Administrateur')</span>
                    </div>
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-agau-blue text-white flex items-center justify-center font-bold text-[10px] md:text-xs shadow-sm border-2 border-white">
                        {{ strtoupper(substr(auth()->user()->prenom, 0, 1) . substr(auth()->user()->nom_famille, 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="px-6 md:px-12 py-10 flex-1">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 px-6 py-4 rounded-xl mb-8 text-sm font-bold shadow-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <i class="bi bi-check2-circle text-emerald-500" style="font-size: 1.3rem;"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-100 text-red-800 px-6 py-4 rounded-xl mb-8 text-sm font-bold shadow-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                    <i class="bi bi-x-lg text-red-500" style="font-size: 1.3rem;"></i>
                    {{ session('error') }}
                </div>
            @endif

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
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }

            if (toggle && sidebar && overlay) {
                toggle.onclick = toggleSidebar;
                overlay.onclick = toggleSidebar;
                if (close) close.onclick = toggleSidebar;
            }
        });
    </script>
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
