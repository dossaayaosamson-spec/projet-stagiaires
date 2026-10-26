@extends('layouts.app')

@section('title', 'Short Stories — AGAU Éditions')

@section('content')
<div class="bg-agau-off-white min-h-screen py-[60px] md:py-[100px] px-[5%] md:px-[8%]">
    <div class="mb-[48px]">
        <p class="text-[0.65rem] font-bold tracking-[3px] uppercase text-agau-blue mb-[10px]">Format Court</p>
        <h1 class="font-serif text-[2.4rem] text-agau-text font-semibold m-0">Toutes nos <span class="text-agau-blue">Short Stories</span></h1>
        <div class="w-[40px] h-[3px] bg-agau-blue mt-[14px] rounded-[2px]"></div>
    </div>

    @if($stories->isEmpty())
        <div class="bg-white border border-agau-border rounded-[12px] p-[60px] text-center shadow-sm">
            <h3 class="font-serif text-[1.5rem] text-agau-text mb-[10px]">Aucune histoire trouvée</h3>
            <p class="text-[0.9rem] text-agau-muted mb-[24px]">De nouvelles histoires arrivent bientôt !</p>
            <a href="{{ route('home') }}" class="bg-agau-blue text-white px-[28px] py-[13px] rounded-[4px] text-[0.75rem] font-bold tracking-[1px] uppercase no-underline hover:bg-agau-blue-dark transition-colors">Retour à l'accueil</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[28px]">
            @foreach($stories as $story)
                <x-story-card 
                    :id="$story->id"
                    :title="$story->titre" 
                    :tags="$story->tags" 
                    :excerpt="$story->extrait" 
                    :author="$story->auteur" 
                    :gradient="$story->gradient" 
                    :cover-path="$story->cover_path"
                />
            @endforeach
        </div>

        <div class="mt-[60px] flex justify-center">
            {{ $stories->links() }}
        </div>
    @endif
</div>
@endsection
