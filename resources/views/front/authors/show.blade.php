@extends('layouts.app')

@section('title', $author->nom . ' — AGAU Éditions')

@section('content')
<div class="bg-agau-off-white min-h-screen py-[60px] md:py-[80px] px-[5%] md:px-[8%]">
    <div class="mb-[32px]">
        <a href="{{ route('front.auteurs.index') }}" class="inline-flex items-center gap-[6px] text-[0.7rem] font-bold text-agau-muted tracking-[1px] uppercase no-underline hover:text-agau-blue transition-colors">
            ← Tous les auteurs
        </a>
    </div>

    <!-- En-tête de l'Auteur -->
    <div class="bg-white border border-agau-border rounded-[12px] p-[32px] md:p-[48px] shadow-sm mb-[48px] flex flex-col md:flex-row gap-[32px] md:gap-[48px] items-center md:items-start">
        @if($author->avatar)
            <img src="{{ asset('storage/' . $author->avatar) }}" alt="{{ $author->nom }}" class="w-[120px] h-[120px] md:w-[150px] md:h-[150px] rounded-full shrink-0 object-cover shadow-lg border-2 border-white">
        @else
            <div class="w-[120px] h-[120px] md:w-[150px] md:h-[150px] rounded-full shrink-0 flex items-center justify-center text-white font-serif text-[3rem] shadow-lg" style="background-color: {{ $author->couleur_avatar }};">
                {{ $author->initiales }}
            </div>
        @endif
        
        <div class="text-center md:text-left flex-1">
            <h1 class="font-serif text-[2.2rem] md:text-[2.8rem] text-agau-text font-semibold m-0 mb-[12px]">{{ $author->nom }}</h1>
            <div class="inline-flex items-center gap-[6px] text-[0.65rem] font-bold tracking-[2px] uppercase bg-agau-blue text-white px-[12px] py-[4px] rounded-[12px] mb-[24px]">
                Auteur AGAU
            </div>
            
            <p class="text-[0.95rem] text-agau-muted leading-[1.8] font-serif italic m-0">
                {{ $author->bio ?? "Maison d'édition AGAU" }}
            </p>
        </div>
    </div>

    <!-- Livres de l'Auteur -->
    <div>
        <div class="flex items-end justify-between mb-[32px]">
            <div>
                <h2 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0">Ses <span class="text-agau-blue">Œuvres</span></h2>
                <div class="w-[30px] h-[3px] bg-agau-blue mt-[10px] rounded-[2px]"></div>
            </div>
            <div class="text-[0.8rem] font-bold text-agau-muted tracking-[1px] uppercase">
                {{ $books->count() }} ouvrage(s)
            </div>
        </div>

        @if($books->isEmpty())
            <div class="bg-white border border-agau-border rounded-[8px] p-[40px] text-center">
                <p class="text-[0.9rem] text-agau-muted m-0">Aucun livre n'est actuellement publié pour cet auteur.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-[24px]">
                @foreach($books as $book)
                    <a href="{{ route('front.livres.show', $book->frontRouteParams()) }}" class="block no-underline group hover:opacity-90 transition-opacity">
                        <div class="aspect-[2/3] rounded-[6px_10px_10px_6px] bg-gradient-to-br relative overflow-hidden shadow-[-6px_8px_32px_rgba(15,74,122,0.15)] flex items-end p-[20px] mb-[16px]" style="background: linear-gradient(to bottom right, {{ $book->couleur_couverture }}, #2A8ACF);">
                            <div class="absolute left-0 top-0 bottom-0 w-[6px] bg-black/25"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0A3050]/90 to-transparent via-transparent to-[55%]"></div>
                            <div class="font-serif text-[0.9rem] italic text-white relative z-[1] leading-[1.4]">{{ $book->titre }}</div>
                        </div>
                        <h3 class="font-serif text-[1.1rem] text-agau-text font-semibold m-0 mb-[4px] group-hover:text-agau-blue transition-colors">{{ $book->titre }}</h3>
                        <p class="text-[0.7rem] font-bold text-agau-muted tracking-[1px] uppercase m-0">{{ number_format($book->prix, 0, ',', ' ') }} FCFA</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
