@extends('layouts.app')

@section('title', 'Résultats de recherche - AGAU Éditions')

@section('content')
<div class="bg-agau-off-white min-h-screen">
    <!-- Header Recherche -->
    <div class="bg-white border-b border-agau-border py-12 px-[6%]">
        <div class="max-w-4xl">
            <p class="text-agau-blue font-bold text-xs uppercase tracking-[3px] mb-4">Recherche globale</p>
            <h1 class="font-serif text-3xl md:text-4xl text-agau-text font-bold mb-8">Résultats pour : <span class="text-agau-blue">"{{ $query }}"</span></h1>
            
            <form action="{{ route('search') }}" method="GET" class="relative max-w-xl">
                <input type="text" name="q" value="{{ $query }}" class="w-full pl-12 pr-4 py-4 bg-agau-off-white border-none rounded-xl text-agau-text focus:ring-2 focus:ring-agau-blue transition-all" placeholder="Rechercher un livre, un auteur...">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-agau-muted text-xl"></i>
            </form>
        </div>
    </div>

    <div class="px-[6%] py-12">
        @if($books->isEmpty() && $authors->isEmpty() && $stories->isEmpty())
            <div class="text-center py-20">
                <i class="bi bi-search text-6xl text-agau-border mb-6 block"></i>
                <h3 class="text-xl text-agau-text font-bold mb-2">Aucun résultat trouvé</h3>
                <p class="text-agau-muted">Essayez d'autres mots-clés ou vérifiez l'orthographe.</p>
            </div>
        @else
            <!-- Livres -->
            @if($books->isNotEmpty())
                <section class="mb-16">
                    <div class="flex items-center gap-4 mb-8">
                        <h2 class="font-serif text-2xl text-agau-text font-bold">Livres ({{ $books->count() }})</h2>
                        <div class="h-[1px] flex-1 bg-agau-border"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                        @foreach($books as $book)
                            <x-book-card 
                                :id="$book->id"
                                :title="$book->titre"
                                :author="$book->author->nom"
                                :category="$book->categorie"
                                :price="$book->prix"
                                :cover="$book->cover_url"
                                :color="$book->couleur_couverture"
                                :badge="$book->badge"
                            />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Auteurs -->
            @if($authors->isNotEmpty())
                <section class="mb-16">
                    <div class="flex items-center gap-4 mb-8">
                        <h2 class="font-serif text-2xl text-agau-text font-bold">Auteurs ({{ $authors->count() }})</h2>
                        <div class="h-[1px] flex-1 bg-agau-border"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach($authors as $author)
                            <x-author-card 
                                :initials="$author->initiales"
                                :name="$author->nom"
                                :bio="$author->bio"
                                :count="$author->books_count ?? $author->books->count()"
                                :avatar="$author->avatar"
                                :color="$author->couleur_avatar"
                            />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Short Stories -->
            @if($stories->isNotEmpty())
                <section class="mb-16">
                    <div class="flex items-center gap-4 mb-8">
                        <h2 class="font-serif text-2xl text-agau-text font-bold">Short Stories ({{ $stories->count() }})</h2>
                        <div class="h-[1px] flex-1 bg-agau-border"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($stories as $story)
                            <x-story-card 
                                :id="$story->id"
                                :title="$story->titre"
                                :excerpt="$story->extrait"
                                :author="$story->author ? $story->author->nom : $story->auteur"
                                :tags="$story->tags"
                                :gradient="$story->gradient"
                                :cover="$story->cover_path"
                            />
                        @endforeach
                    </div>
                </section>
            @endif
        @endif
    </div>
</div>
@endsection
