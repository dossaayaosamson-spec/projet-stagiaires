<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-agau-off-white border-r border-agau-border/40 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full px-6 py-8">
        <div class="mb-10 px-2 flex justify-between items-center">
            <a href="{{ route('home') }}" class="no-underline">
                <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" style="width: 80px" alt="AGAU Éditions">
                <p class="text-[10px] text-agau-muted font-bold mt-1 uppercase tracking-[2px]">Maison d'éditions</p>
            </a>
            <button id="sidebar-close" class="lg:hidden text-2xl text-agau-muted bg-transparent border-none cursor-pointer">
                <i class="bi bi-x"></i>
            </button>
        </div>
        
        <nav class="flex-1 flex flex-col gap-1 overflow-y-auto pr-2 custom-scrollbar">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                <i class="bi bi-columns-gap" style="font-size: 1.1rem;"></i>
                <span>Tableau de bord</span>
            </a>
            
            <div class="py-2">
                <div class="h-[1px] bg-agau-border/30 mx-4"></div>
            </div>

            <a href="{{ route('client.library') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('client.library') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                <i class="bi bi-book-half" style="font-size: 1.1rem;"></i>
                <span>Bibliothèque</span>
            </a>
            
            <a href="{{ route('client.reading') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('client.reading') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                <i class="bi bi-bookmark-heart" style="font-size: 1.1rem;"></i>
                <span>En cours de lecture</span>
            </a>
            
            <a href="{{ route('client.favorites') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('client.favorites') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                <i class="bi bi-heart" style="font-size: 1.1rem;"></i>
                <span>Mes favoris</span>
                <span class="ml-auto bg-agau-blue/10 text-agau-blue text-[10px] font-bold px-2 py-0.5 rounded-full">{{ auth()->user()->livresFavoris()->count() }}</span>
            </a>

            <div class="py-2">
                <div class="h-[1px] bg-agau-border/30 mx-4"></div>
            </div>

            <div class="px-4 py-2 text-[10px] font-bold text-agau-muted/40 uppercase tracking-widest">Catalogue</div>
            
            <a href="{{ route('front.catalogue') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('front.catalogue') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                <i class="bi bi-grid" style="font-size: 1.1rem;"></i>
                <span>Tous les livres</span>
            </a>
            
            <a href="{{ route('front.stories.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('front.stories.index') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                <i class="bi bi-journal-text" style="font-size: 1.1rem;"></i>
                <span>Short Stories</span>
                <span class="ml-auto bg-agau-blue/10 text-agau-blue text-[10px] font-bold px-2 py-0.5 rounded-full">{{ \App\Models\ShortStory::count() }}</span>
            </a>

            <div class="py-2">
                <div class="h-[1px] bg-agau-border/30 mx-4"></div>
            </div>

            <div class="px-4 py-2 text-[10px] font-bold text-agau-muted/40 uppercase tracking-widest">Mon compte</div>

            <a href="{{ route('client.orders') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('client.orders') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                <i class="bi bi-bag-check" style="font-size: 1.1rem;"></i>
                <span>Mes commandes</span>
                <span class="ml-auto bg-agau-blue/10 text-agau-blue text-[10px] font-bold px-2 py-0.5 rounded-full">{{ auth()->user()->orders()->count() }}</span>
            </a>

            <a href="{{ route('client.subscription') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('client.subscription') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                <i class="bi bi-credit-card" style="font-size: 1.1rem;"></i>
                <span>Abonnement</span>
            </a>

            <a href="{{ route('client.candidatures') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('client.candidatures') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                <i class="bi bi-file-earmark-person" style="font-size: 1.1rem;"></i>
                <span>Mes candidatures</span>
                @php
                    $userCandidaturesCount = \App\Models\CandidatureEcla::where('email', auth()->user()->email)->count();
                @endphp
                @if($userCandidaturesCount > 0)
                    <span class="ml-auto bg-agau-blue/10 text-agau-blue text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $userCandidaturesCount }}</span>
                @endif
            </a>

            @if(strtolower(auth()->user()->role) === 'auteur')
                <div class="py-2">
                    <div class="h-[1px] bg-agau-border/30 mx-4"></div>
                </div>

                <div class="px-4 py-2 text-[10px] font-bold text-agau-blue/60 uppercase tracking-widest">Espace Auteur</div>

                <a href="{{ route('author.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('author.dashboard') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-speedometer2" style="font-size: 1.1rem;"></i>
                    <span>Tableau de bord Auteur</span>
                </a>

                <a href="{{ route('author.profile') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('author.profile') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-person-badge" style="font-size: 1.1rem;"></i>
                    <span>Mon Profil Public</span>
                </a>
            @else
                <div class="py-2">
                    <div class="h-[1px] bg-agau-border/30 mx-4"></div>
                </div>

                <a href="{{ route('client.become-author') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('client.become-author*') ? 'bg-agau-blue/10 text-agau-blue' : 'text-agau-muted hover:text-agau-blue hover:bg-agau-blue/5' }}">
                    <i class="bi bi-feather" style="font-size: 1.1rem;"></i>
                    <span>Devenir Auteur</span>
                    @php
                        $authorAppCount = auth()->user()->authorApplications()->count();
                    @endphp
                    @if($authorAppCount > 0)
                        <span class="ml-auto bg-amber-100 text-amber-800 text-[9px] font-bold px-2 py-0.5 rounded-full">Suivi</span>
                    @endif
                </a>
            @endif
        </nav>

        <div class="mt-auto flex flex-col gap-1 border-t border-agau-border/30 pt-6">
            <a href="{{ route('client.profile') }}" class="flex items-center gap-3 text-agau-muted px-4 py-2 hover:text-agau-blue hover:bg-agau-blue/5 rounded-lg transition-all text-sm font-medium">
                <i class="bi bi-person-gear" style="font-size: 1.1rem;"></i>
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
