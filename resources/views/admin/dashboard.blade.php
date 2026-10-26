@extends('layouts.admin')

@section('title', 'Tableau de Bord — Admin AGAU Éditions')

@section('content')
<div class="space-y-12 max-w-7xl mx-auto px-4">
    <!-- Greeting & Editorial Header -->
    <section>
        <h1 class="font-lora text-4xl md:text-5xl font-bold text-agau-text mb-2">Bienvenue, {{ auth()->user()->prenom }}.</h1>
        <p class="text-agau-muted font-medium">Voici l'état actuel de votre catalogue littéraire numérique.</p>
    </section>

    <!-- Stats Grid (Bento Style) -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Members -->
        <div class="bg-white p-6 rounded-xl border border-agau-border/40 shadow-sm flex flex-col justify-between group hover:shadow-md transition-all">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[2px] text-agau-muted opacity-60">Membres</span>
                <h2 class="text-4xl font-lora font-bold mt-2 text-agau-blue-dark">{{ number_format($stats['users'], 0, ',', ' ') }}</h2>
            </div>
            <div class="mt-4 flex items-center text-agau-blue gap-1">
                <i class="bi bi-graph-up-arrow" style="font-size: 1.3rem"></i>
                <span class="text-[11px] font-bold">Communauté en éveil</span>
            </div>
        </div>

        <!-- Catalogue -->
        <div class="bg-white p-6 rounded-xl border border-agau-border/40 shadow-sm flex flex-col justify-between group hover:shadow-md transition-all">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[2px] text-agau-muted opacity-60">Catalogue</span>
                <h2 class="text-4xl font-lora font-bold mt-2 text-agau-text">{{ $stats['books'] }}</h2>
            </div>
            <div class="mt-4 flex items-center text-agau-blue gap-1">
                <i class="bi bi-justify-left" style="font-size: 1.3rem"></i>
                <span class="text-[11px] font-bold">Ouvrages actifs</span>
            </div>
        </div>

        <!-- Revenue (Total CA) -->
        <div class="bg-agau-blue/5 p-6 rounded-xl border border-agau-blue/10 shadow-sm flex flex-col justify-between transition-all">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[2px] text-agau-blue opacity-80">CA Total</span>
                <div class="flex items-baseline gap-1 mt-2">
                    <h2 class="text-4xl font-lora font-bold text-agau-blue-dark">{{ number_format($stats['sales'], 0, ',', ' ') }}</h2>
                    <span class="text-xs font-lora italic text-agau-blue-dark">FCFA</span>
                </div>
            </div>
            <div class="mt-4 bg-agau-blue/10 px-3 py-1 rounded-full w-fit">
                <span class="text-[9px] text-agau-blue-dark font-bold uppercase tracking-widest">croissance stable</span>
            </div>
        </div>

        <!-- Chronicles -->
        <div class="bg-white p-6 rounded-xl border border-agau-border/40 shadow-sm flex flex-col justify-between group hover:shadow-md transition-all">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[2px] text-agau-muted opacity-60">Chroniques</span>
                <h2 class="text-4xl font-lora font-bold mt-2 text-agau-text">{{ $stats['stories'] }}</h2>
            </div>
            <div class="mt-4 flex items-center text-agau-muted gap-1">
                <i class="bi bi-pencil-square" style="font-size: 1.3rem"></i>
                <span class="text-[11px] font-bold">Récits publiés</span>
            </div>
        </div>
    </section>

    <!-- Main Content List: Nouveautés littéraires -->
    <section class="space-y-6">
        <div class="flex justify-between items-end px-2">
            <h3 class="font-lora text-2xl font-bold text-agau-text">Nouveautés littéraires</h3>
            <a href="{{ route('admin.books.index') }}" class="text-agau-blue text-sm font-bold hover:underline flex items-center gap-1 transition-all">
                Voir tout le catalogue
                <i class="bi bi-arrow-right-short text-[18px]"></i>
            </a>
        </div>

        <!-- Literary List -->
        <div class="grid grid-cols-1 gap-4">
            @foreach($latestBooks as $book)
            <div class="group bg-white hover:border-agau-blue/40 border border-agau-border/30 transition-all p-4 rounded-xl flex items-center gap-6 shadow-sm shadow-agau-blue/5">
                <div class="relative w-16 h-24 flex-shrink-0 shadow-lg rounded-sm overflow-hidden" style="background: {{ $book->couleur_couverture }}">
                    @if($book->cover_path)
                        <img src="{{ asset('storage/' . $book->cover_path) }}" class="w-full h-full object-cover">
                    @endif
                    <div class="absolute inset-0 bg-black/5"></div>
                </div>
                
                <div class="flex-1 flex flex-col min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <h4 class="font-lora text-lg font-bold leading-none truncate text-agau-text group-hover:text-agau-blue transition-colors">{{ $book->titre }}</h4>
                        @if($book->is_bestseller)
                            <span class="bg-amber-100 text-amber-700 text-[8px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full">PREMIUM SELECTION</span>
                        @endif
                    </div>
                    <span class="text-xs text-agau-muted font-medium italic">{{ $book->author->nom }}</span>
                </div>

                <div class="text-right flex flex-col items-end gap-1 px-8 border-r border-agau-border/30 hidden md:flex">
                    <span class="text-sm font-bold font-lora text-agau-blue-dark">{{ number_format($book->prix, 0, ',', ' ') }} FCFA</span>
                    <span class="text-[9px] text-agau-muted uppercase tracking-[1px] font-bold">Prix Public</span>
                </div>

                <div class="flex items-center gap-2 pl-4">
                    <a href="{{ route('admin.books.edit', $book) }}" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Modifier">
                        <i class="bi bi-pencil-square" style="font-size: 1.3rem;"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Bottom Secondary Section -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-12 pb-10">
        <!-- Recent Activity -->
        <div class="space-y-4">
            <h3 class="font-lora text-xl font-bold text-agau-text">Flux d'activités</h3>
            <div class="bg-white border border-agau-border/30 p-6 rounded-xl space-y-6 shadow-sm">
                @foreach($recentUsers as $user)
                    <div class="flex gap-4 items-center">
                        <div class="w-2 h-2 rounded-full bg-agau-blue shrink-0"></div>
                        <p class="text-sm text-agau-text flex-1">
                            <span class="font-bold">Nouveau membre</span> {{ $user->prenom }} a rejoint la communauté AGAU.ED.
                        </p>
                        <span class="text-[9px] text-agau-muted font-bold uppercase tracking-tighter">{{ $user->created_at->diffForHumans(null, true) }}</span>
                    </div>
                @endforeach
                @if($recentUsers->isEmpty())
                    <p class="text-sm text-agau-muted italic">Aucune activité récente.</p>
                @endif
            </div>
        </div>

        <!-- Featured Actions/Authors -->
        <div class="space-y-4">
            <h3 class="font-lora text-xl font-bold text-agau-text">Actions Rapides</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.authors.create') }}" class="bg-white p-5 rounded-xl border border-agau-border/30 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-agau-blue/30 transition-all no-underline group text-agau-text">
                    <div class="w-10 h-10 rounded-full bg-agau-blue/5 flex items-center justify-center text-agau-blue group-hover:bg-agau-blue group-hover:text-white transition-all">
                        <i class="bi bi-person-add" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold leading-none">Inscrire un auteur</p>
                        <p class="text-[9px] text-agau-muted mt-1 uppercase tracking-tighter">Répertoire</p>
                    </div>
                </a>
                <a href="{{ route('admin.stories.create') }}" class="bg-white p-5 rounded-xl border border-agau-border/30 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-agau-blue/30 transition-all no-underline group text-agau-text">
                    <div class="w-10 h-10 rounded-full bg-agau-blue/5 flex items-center justify-center text-agau-blue group-hover:bg-agau-blue group-hover:text-white transition-all">
                        <i class="bi bi-journal-plus" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold leading-none">Rédiger un récit</p>
                        <p class="text-[9px] text-agau-muted mt-1 uppercase tracking-tighter">Chroniques</p>
                    </div>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
