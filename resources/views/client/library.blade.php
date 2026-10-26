@extends('layouts.dashboard')

@section('title', 'Ma Bibliothèque — AGAU Éditions')

@section('content')
<div class="max-w-[1200px] mx-auto">
    <div class="mb-[32px]">
        <h1 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0 mb-[8px]">Ma <span class="text-agau-blue">Bibliothèque</span></h1>
        <p class="text-[0.85rem] text-agau-muted m-0 font-medium">Retrouvez ici tous les ouvrages que vous avez acquis.</p>
    </div>

    @if($livresAchetes->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[24px]">
            @foreach($livresAchetes as $livre)
                <div class="bg-white border border-agau-border rounded-[10px] p-[20px] shadow-[0_4px_20px_rgba(15,74,122,0.03)] hover:shadow-[0_8px_30px_rgba(15,74,122,0.08)] transition-all duration-300">
                    <div class="flex gap-[16px] mb-[20px]">
                        <div class="w-[60px] aspect-[2/3] rounded-[4px] relative overflow-hidden shadow-[0_2px_8px_rgba(0,0,0,0.15)] shrink-0">
                            @if($livre->cover_url)
                                <img src="{{ $livre->cover_url }}" alt="{{ $livre->titre }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full" style="background: {{ $livre->couleur_couverture }}"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-[0.95rem] font-bold text-agau-text leading-[1.3] m-0 mb-[4px] truncate">{{ $livre->titre }}</h3>
                            <p class="text-[0.7rem] text-agau-muted mb-[8px] m-0">{{ $livre->author->nom ?? 'Inconnu' }}</p>
                            <span class="text-[0.6rem] font-bold text-agau-blue uppercase tracking-[0.5px]">Livre</span>
                        </div>
                    </div>
                    <a href="{{ route('read.book', $livre->id) }}" class="w-full bg-agau-blue text-white no-underline text-center block py-[10px] text-[0.7rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all hover:bg-agau-blue-dark">Lire maintenant</a>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-agau-border rounded-[10px] p-[60px] shadow-[0_4px_20px_rgba(15,74,122,0.03)] text-center">
            <div class="w-[80px] h-[80px] bg-agau-blue/5 rounded-full flex items-center justify-center mx-auto mb-[24px]">
                <span class="text-[40px]">📚</span>
            </div>
            <h2 class="text-[1.2rem] font-bold text-agau-text mb-[12px]">Votre bibliothèque est vide</h2>
            <p class="text-agau-muted max-w-[400px] mx-auto mb-[32px] text-[0.9rem] leading-[1.6]">
                Vous n'avez pas encore acheté d'ouvrages ou souscrit à un abonnement permettant d'accéder au catalogue.
            </p>
            <a href="{{ route('front.catalogue') }}" class="inline-block bg-agau-blue text-white no-underline py-[12px] px-[32px] text-[0.75rem] font-bold tracking-[1.5px] uppercase rounded-[4px] transition-all duration-200 shadow-[0_4px_12px_rgba(27,108,168,0.25)] hover:bg-[#1B5E8A] hover:-translate-y-[2px]">
                Explorer le catalogue
            </a>
        </div>
    @endif
</div>
@endsection
