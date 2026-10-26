@extends('layouts.app')

@section('title', 'Nos Auteurs — AGAU Éditions')

@section('content')
<div class="bg-agau-blue-deep min-h-screen py-[60px] md:py-[80px] px-[5%] md:px-[8%]" >
    <div class="mb-[48px] text-center">
        <p class="text-[0.7rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px]">Découvrez les plumes de la maison</p>
        <h1 class="font-serif text-[2.4rem] text-white font-semibold m-0">Nos <span class="text-[#5BC4F5]">Auteurs</span></h1>
        <div class="w-[40px] h-[3px] bg-white mt-[14px] rounded-[2px] mx-auto"></div>
    </div>

    @if($authors->isEmpty())
        <div class="bg-white border border-agau-border rounded-[12px] p-[60px] text-center shadow-sm">
            <h3 class="font-serif text-[1.5rem] text-agau-text mb-[10px]">Aucun auteur trouvé</h3>
            <p class="text-[0.9rem] text-agau-muted">Notre liste d'auteurs est en cours de préparation.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-[24px]">
            @foreach($authors as $author)
                <a href="{{ route('front.auteurs.show', $author->frontRouteParams()) }}" class="block no-underline group hover:-translate-y-[6px] transition-transform duration-300">
                    <x-author-card 
                        :initials="$author->initiales" 
                        :name="$author->nom" 
                        :bio="$author->bio" 
                        :count="$author->books_count" 
                        :color="$author->couleur_avatar" 
                        :avatar="$author->avatar"
                    />
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
