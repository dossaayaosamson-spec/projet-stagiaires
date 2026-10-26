@extends('layouts.dashboard')

@section('title', 'Lectures en cours — AGAU Éditions')

@section('content')
<div class="max-w-[1200px] mx-auto">
    <div class="mb-[32px]">
        <h1 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0 mb-[8px]">En cours de <span class="text-agau-blue">Lecture</span></h1>
        <p class="text-[0.85rem] text-agau-muted m-0 font-medium">Reprenez votre lecture là où vous l'avez laissée.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[24px]">
        @forelse($books as $book)
            <!-- Book progress item -->
            <div class="bg-white border border-agau-border rounded-[10px] p-[20px] shadow-[0_4px_20px_rgba(15,74,122,0.03)] hover:shadow-[0_8px_30px_rgba(15,74,122,0.08)] transition-all duration-300">
                <div class="flex gap-[16px] mb-[20px]">
                     <div class="w-[60px] aspect-[2/3] rounded-[4px] relative overflow-hidden shadow-[0_2px_8px_rgba(0,0,0,0.15)] shrink-0">
                        @if($book->cover_url)
                            <img src="{{ $book->cover_url }}" alt="{{ $book->titre }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full" style="background: {{ $book->couleur_couverture }}"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[0.95rem] font-bold text-agau-text leading-[1.3] m-0 mb-[4px] truncate">{{ $book->titre }}</h3>
                        <p class="text-[0.7rem] text-agau-muted mb-[8px] m-0">{{ $book->author->nom }}</p>
                        <span class="text-[0.6rem] font-bold text-agau-blue uppercase tracking-[0.5px]">Roman</span>
                    </div>
                </div>
                <div class="flex items-center gap-[12px] mb-[16px]">
                    <div class="h-[6px] rounded-[3px] bg-agau-border flex-1 overflow-hidden">
                        <div class="h-full bg-agau-blue rounded-[3px]" style="width:{{ $book->pivot->progress }}%"></div>
                    </div>
                    <span class="text-[0.7rem] font-bold text-agau-text">{{ $book->pivot->progress }}%</span>
                </div>
                <a href="{{ route('front.livres.show', $book->frontRouteParams()) }}" class="w-full bg-agau-blue text-white no-underline text-center block py-[10px] text-[0.7rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all hover:bg-agau-blue-dark">Reprendre</a>
            </div>
        @empty
            <div class="col-span-full bg-white border border-agau-border rounded-[10px] p-[60px] text-center">
                <div class="w-[80px] h-[80px] bg-agau-blue/5 rounded-full flex items-center justify-center mx-auto mb-[24px]">
                    <span class="text-[40px]"><i class="bi bi-book"></i></span>
                </div>
                <h2 class="text-[1.2rem] font-bold text-agau-text mb-[12px]">Aucune lecture en cours</h2>
                <p class="text-agau-muted max-w-[400px] mx-auto text-[0.9rem] leading-[1.6]">
                    Vos lectures actives apparaîtront ici pour un accès rapide.
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection
