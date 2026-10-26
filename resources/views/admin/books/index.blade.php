@extends('layouts.admin')

@section('title', 'Gestion des Livres — Admin AGAU Éditions')
@section('header-title', 'Patrimoine Littéraire')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Bibliothèque</h1>
            <p class="text-sm text-agau-muted font-medium">Inventaire complet de votre patrimoine littéraire.</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="bg-agau-blue text-white px-6 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-plus-lg" style="font-size: 1.3rem"></i>
            Ajouter un ouvrage
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-8 py-5">Œuvre & Auteur</th>
                    <th class="px-8 py-5">Catégorie</th>
                    <th class="px-8 py-5">Prix</th>
                    <th class="px-8 py-5 text-center">Gestion</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($books as $book)
                    <tr class="hover:bg-agau-blue/5 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 aspect-[2/3] rounded shadow-sm overflow-hidden relative border border-agau-border/20" style="background: {{ $book->couleur_couverture }}">
                                    @if($book->cover_path)
                                        <img src="{{ asset('storage/' . $book->cover_path) }}" class="absolute inset-0 w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-agau-text group-hover:text-agau-blue transition-colors">{{ $book->titre }}</span>
                                    <span class="text-[11px] text-agau-muted font-medium italic">{{ $book->author->nom }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="bg-agau-border/10 text-agau-muted px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">{{ $book->categorie }}</span>
                        </td>
                        <td class="px-8 py-5 font-lora font-bold text-agau-blue-dark">
                            {{ number_format($book->prix, 0, ',', ' ') }} <span class="text-[10px] font-sans">FCFA</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex gap-3 justify-center">
                                <a href="{{ route('front.livres.show', $book->frontRouteParams()) }}" target="_blank" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Voir sur le site">
                                    <i class="bi bi-eye" style="font-size: 1.3rem;"></i>
                                </a>
                                <a href="{{ route('admin.books.edit', $book) }}" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Éditer">
                                    <i class="bi bi-pencil-square" style="font-size: 1.3rem;"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.books.destroy', $book) }}" class="inline" onsubmit="return confirm('Archiver définitivement cet ouvrage ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 hover:bg-red-50 rounded-full text-agau-muted hover:text-red-600 transition-all border-none bg-transparent cursor-pointer">
                                        <i class="bi bi-trash" style="font-size: 1.3rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <span class="material-symbols-outlined text-4xl text-agau-muted/20">search_off</span>
                                <p class="text-agau-muted font-lora italic">Aucun ouvrage trouvé dans les archives.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($books->hasPages())
        <div class="mt-8">
            {{ $books->links() }}
        </div>
    @endif
</div>
@endsection
