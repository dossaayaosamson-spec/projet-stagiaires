@extends('layouts.app')

@section('title', 'Page Non Trouvée - AGAU Éditions')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center bg-agau-off-white px-6">
    <div class="max-w-2xl w-full text-center py-20">
        <div class="relative mb-12">
            <h1 class="text-[12rem] font-black text-agau-blue/25 leading-none select-none">404</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <img src="{{ asset('images/AGAU LOGO BLEU.png') }}" alt="AGAU" class="w-24 opacity-50">
            </div>
        </div>

        <h2 class="font-serif text-3xl md:text-4xl text-agau-blue-deep font-bold mb-6">Oups ! Ce chapitre est introuvable.</h2>
        
        <p class="text-agau-muted text-lg mb-10 leading-relaxed max-w-lg mx-auto">
            Il semble que la page que vous recherchez ait été déplacée ou n'existe plus. 
            Peut-être est-elle cachée entre deux pages d'un vieux grimoire ?
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('home') }}" class="bg-agau-blue text-white px-8 py-4 rounded-lg font-bold uppercase tracking-wider text-sm hover:bg-agau-blue-dark transition-all shadow-md no-underline">
                Retour à l'accueil
            </a>
            <a href="{{ route('front.catalogue') }}" class="border-2 border-agau-blue text-agau-blue px-8 py-4 rounded-lg font-bold uppercase tracking-wider text-sm hover:bg-agau-blue hover:text-white transition-all no-underline">
                Découvrir nos livres
            </a>
        </div>

        <div class="mt-20 pt-10 border-t border-agau-border">
            <p class="text-sm text-agau-muted italic">
                Besoin d'aide ? <a href="{{ route('front.contact') }}" class="text-agau-blue font-bold no-underline hover:underline">Contactez-nous</a>
            </p>
        </div>
    </div>
</div>
@endsection
