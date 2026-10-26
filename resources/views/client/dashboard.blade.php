@extends('layouts.dashboard')

@section('content')
<div class="space-y-12 max-w-7xl mx-auto px-4">
    <!-- Greeting & Welcome Header -->
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="font-lora text-4xl md:text-5xl font-bold text-agau-text mb-2">
                Bienvenue, <span class="text-agau-blue">{{ auth()->user()->alias ?? auth()->user()->prenom ?? 'Lecteur' }}</span>.
            </h1>
            <p class="text-agau-muted font-medium">Heureux de vous revoir ! Prêt pour de nouvelles découvertes littéraires ?</p>
        </div>
        
        @if($latestReadBook)
        <a href="{{ route('read.book', $latestReadBook->id) }}" class="bg-agau-blue text-white px-6 py-3 text-[0.7rem] font-bold tracking-[1.5px] uppercase rounded-lg transition-all duration-300 shadow-lg shadow-agau-blue/20 hover:bg-agau-blue-dark hover:-translate-y-1 no-underline flex items-center gap-2">
            <i class="bi bi-play-fill text-lg"></i>
            Reprendre : {{ Str::limit($latestReadBook->titre, 25) }}
        </a>
        @else
        <a href="{{ route('client.library') }}" class="bg-agau-blue text-white px-6 py-3 text-[0.7rem] font-bold tracking-[1.5px] uppercase rounded-lg transition-all duration-300 shadow-lg shadow-agau-blue/20 hover:bg-agau-blue-dark hover:-translate-y-1 no-underline">
            Explorer la bibliothèque
        </a>
        @endif
    </section>

    <!-- Stats Grid (Bento Style) -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- In Progress -->
        <a href="{{ route('client.reading') }}" class="bg-white p-6 rounded-xl border border-agau-border/40 shadow-sm flex flex-col justify-between group hover:shadow-md transition-all no-underline">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[2px] text-agau-muted opacity-60">Lectures</span>
                <h2 class="text-4xl font-lora font-bold mt-2 text-agau-blue-dark">{{ $stats['in_progress'] }}</h2>
            </div>
            <div class="mt-4 flex items-center text-agau-blue gap-2">
                <i class="bi bi-book" style="font-size: 1.2rem"></i>
                <span class="text-[11px] font-bold">En cours de lecture</span>
            </div>
        </a>

        <!-- Finished -->
        <a href="{{ route('client.library') }}" class="bg-white p-6 rounded-xl border border-agau-border/40 shadow-sm flex flex-col justify-between group hover:shadow-md transition-all no-underline">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[2px] text-agau-muted opacity-60">Terminés</span>
                <h2 class="text-4xl font-lora font-bold mt-2 text-agau-text">{{ $stats['finished'] }}</h2>
            </div>
            <div class="mt-4 flex items-center text-emerald-600 gap-2">
                <i class="bi bi-check-circle-fill" style="font-size: 1.1rem"></i>
                <span class="text-[11px] font-bold">Collection complétée</span>
            </div>
        </a>

        <!-- Reading Time (Est.) -->
        <div class="bg-agau-blue/5 p-6 rounded-xl border border-agau-blue/10 shadow-sm flex flex-col justify-between transition-all">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[2px] text-agau-blue opacity-80">Temps</span>
                <div class="flex items-baseline gap-1 mt-2">
                    <h2 class="text-4xl font-lora font-bold text-agau-blue-dark">{{ intval($stats['reading_time']) }}</h2>
                    <span class="text-xs font-lora italic text-agau-blue-dark">min</span>
                </div>
            </div>
            <div class="mt-4 bg-agau-blue/10 px-3 py-1 rounded-full w-fit">
                <span class="text-[9px] text-agau-blue-dark font-bold uppercase tracking-widest">Immersion Totale</span>
            </div>
        </div>

        <!-- Favorites -->
        <a href="{{ route('client.favorites') }}" class="bg-white p-6 rounded-xl border border-agau-border/40 shadow-sm flex flex-col justify-between group hover:shadow-md transition-all no-underline">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[2px] text-agau-muted opacity-60">Favoris</span>
                <h2 class="text-4xl font-lora font-bold mt-2 text-agau-text">{{ $stats['favorites'] }}</h2>
            </div>
            <div class="mt-4 flex items-center text-amber-500 gap-2">
                <i class="bi bi-star-fill" style="font-size: 1.1rem"></i>
                <span class="text-[11px] font-bold">Coups de cœur</span>
            </div>
        </a>
    </section>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Left Column: In Progress List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex justify-between items-end px-2">
                <h3 class="font-lora text-2xl font-bold text-agau-text">Mes Lectures en cours</h3>
                <a href="{{ route('client.reading') }}" class="text-agau-blue text-sm font-bold hover:underline flex items-center gap-1 transition-all">
                    Voir tout
                    <span class="material-symbols-outlined text-[18px]">arrow_right_alt</span>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse($inProgressBooks as $book)
                <div class="group bg-white hover:border-agau-blue/40 border border-agau-border/30 transition-all p-4 rounded-xl flex items-center gap-6 shadow-sm shadow-agau-blue/5">
                    <div class="relative w-16 h-24 flex-shrink-0 shadow-lg rounded-sm overflow-hidden" style="background: {{ $book->couleur_couverture }}">
                        @if($book->cover_url)
                            <img src="{{ $book->cover_url }}" class="w-full h-full object-cover">
                        @elseif($book->cover_path)
                            <img src="{{ asset('storage/' . $book->cover_path) }}" class="w-full h-full object-cover">
                        @endif
                        <div class="absolute inset-0 bg-black/5"></div>
                    </div>
                    
                    <div class="flex-1 flex flex-col min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <h4 class="font-lora text-lg font-bold leading-none truncate text-agau-text group-hover:text-agau-blue transition-colors">{{ $book->titre }}</h4>
                        </div>
                        <span class="text-xs text-agau-muted font-medium italic mb-3">{{ $book->author->nom ?? 'Auteur inconnu' }}</span>
                        
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-1.5 bg-agau-blue/5 rounded-full overflow-hidden">
                                <div class="h-full bg-agau-blue rounded-full transition-all duration-500" style="width: {{ $book->pivot->progress }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-agau-blue">{{ $book->pivot->progress }}%</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pl-4">
                        <a href="{{ route('read.book', $book->id) }}" class="w-10 h-10 rounded-full bg-agau-blue/5 flex items-center justify-center text-agau-blue hover:bg-agau-blue hover:text-white transition-all shadow-sm" title="Reprendre la lecture">
                            <i class="bi bi-play-fill" style="font-size: 1.4rem;"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="bg-white border border-dashed border-agau-border/60 rounded-xl p-12 text-center">
                    <div class="w-16 h-16 bg-agau-blue/5 rounded-full flex items-center justify-center mx-auto mb-4 text-agau-blue/40">
                        <i class="bi bi-book" style="font-size: 2rem;"></i>
                    </div>
                    <p class="text-agau-muted font-medium">Vous n'avez pas encore de livres en cours de lecture.</p>
                    <a href="{{ route('front.catalogue') }}" class="inline-block mt-4 text-agau-blue font-bold text-sm hover:underline">Découvrir le catalogue</a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-10">
            <!-- Recommendations -->
            <div class="space-y-4">
                <h3 class="font-lora text-xl font-bold text-agau-text">Recommandation</h3>
                @if($recommendedBook)
                <a href="{{ route('front.livres.show', $recommendedBook->frontRouteParams()) }}" class="block group bg-white border border-agau-border/30 rounded-xl p-5 shadow-sm hover:shadow-md transition-all no-underline">
                    <div class="flex gap-4">
                        <div class="relative w-20 h-28 flex-shrink-0 shadow-lg rounded-sm overflow-hidden" style="background: {{ $recommendedBook->couleur_couverture }}">
                            @if($recommendedBook->cover_url)
                                <img src="{{ $recommendedBook->cover_url }}" class="w-full h-full object-cover">
                            @elseif($recommendedBook->cover_path)
                                <img src="{{ asset('storage/' . $recommendedBook->cover_path) }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-black/5"></div>
                        </div>
                        <div class="flex flex-col justify-center min-w-0">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-amber-600 mb-1">Pour vous</span>
                            <h4 class="font-lora text-base font-bold text-agau-text group-hover:text-agau-blue transition-colors truncate">{{ $recommendedBook->titre }}</h4>
                            <p class="text-[11px] text-agau-muted italic">{{ $recommendedBook->author->nom ?? 'Découverte' }}</p>
                        </div>
                    </div>
                </a>
                @endif

                @if(!auth()->user()->isAuthor())
                <!-- Become Author Card -->
                <div class="bg-gradient-to-br from-agau-blue/10 via-white to-agau-blue/5 border border-agau-blue/20 rounded-xl p-5 shadow-sm space-y-3">
                    <div class="flex items-center gap-2 text-agau-blue text-[10px] font-bold uppercase tracking-widest">
                        <i class="bi bi-feather"></i>
                        <span>Espace Créateurs</span>
                    </div>
                    <h4 class="font-lora text-base font-bold text-agau-text m-0">Vous êtes écrivain ?</h4>
                    <p class="text-xs text-agau-muted leading-relaxed m-0">
                        Proposez votre manuscrit à notre comité éditorial et rejoignez le catalogue officiel d'AGAU Éditions.
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('client.become-author') }}" class="inline-flex items-center gap-2 bg-agau-blue text-white px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-agau-blue-dark transition-all no-underline shadow-sm shadow-agau-blue/20">
                            <span>Postuler</span>
                            <i class="bi bi-arrow-right text-[11px]"></i>
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Recent Orders -->
            <div class="space-y-4">
                <h3 class="font-lora text-xl font-bold text-agau-text">Dernières Activités</h3>
                <div class="bg-white border border-agau-border/30 rounded-xl p-6 shadow-sm divide-y divide-agau-border/20">
                    @forelse($recentOrders as $order)
                    <div class="py-3 first:pt-0 last:pb-0 flex justify-between items-center gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-agau-text truncate">Commande #{{ $order->id }}</p>
                            <p class="text-[10px] text-agau-muted">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-[10px] font-bold text-agau-blue-dark mb-1">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
                            <span class="text-[8px] font-bold uppercase tracking-tighter px-2 py-0.5 rounded {{ $order->statut == 'paye' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $order->statut }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-agau-muted italic text-center py-4">Aucune commande récente.</p>
                    @endforelse

                    @if($activeSubscription)
                    <div class="pt-4 mt-2">
                        <div class="bg-agau-blue/5 rounded-lg p-3 border border-agau-blue/10">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-[9px] font-bold text-agau-blue-dark uppercase tracking-widest">Abonnement</span>
                                <span class="text-[8px] font-bold text-emerald-600 bg-emerald-100 px-1.5 py-0.5 rounded">ACTIF</span>
                            </div>
                            <p class="text-[11px] font-bold text-agau-text">Plan {{ ucfirst($activeSubscription->plan) }}</p>
                            <p class="text-[9px] text-agau-muted">Jusqu'au {{ $activeSubscription->date_fin->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <a href="{{ route('client.orders') }}" class="block text-center text-[10px] font-bold text-agau-blue uppercase tracking-widest mt-6 hover:underline no-underline">
                        Voir tout l'historique
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
