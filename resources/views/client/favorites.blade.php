@extends('layouts.dashboard')

@section('title', 'Mes Favoris — AGAU Éditions')

@section('content')
<div class="max-w-[1200px] mx-auto">
    <div class="mb-[32px]">
        <h1 class="font-serif text-[1.8rem] text-agau-text font-semibold m-0 mb-[8px]">Mes <span class="text-agau-blue">Favoris</span></h1>
        <p class="text-[0.85rem] text-agau-muted m-0 font-medium">Retrouvez les ouvrages qui vous ont le plus marqué.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-[16px]">
        @forelse($favorites as $book)
            <x-book-card :book="$book" />
        @empty
            <div class="col-span-full bg-white border border-agau-border rounded-[10px] p-[60px] text-center">
                <div class="w-[80px] h-[80px] bg-agau-blue/5 rounded-full flex items-center justify-center mx-auto mb-[24px]">
                    <span class="text-[40px]">❤️</span>
                </div>
                <h2 class="text-[1.2rem] font-bold text-agau-text mb-[12px]">Aucun favori pour le moment</h2>
                <p class="text-agau-muted max-w-[400px] mx-auto text-[0.9rem] leading-[1.6]">
                    Ajoutez des livres à vos favoris en cliquant sur l'icône cœur lors de votre navigation.
                </p>
                <a href="{{ route('front.catalogue') }}" class="inline-block mt-[20px] text-agau-blue font-bold text-[0.75rem] uppercase tracking-[1px] no-underline hover:underline">Découvrir des livres</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
