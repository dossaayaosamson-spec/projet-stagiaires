@extends('layouts.admin')

@section('title', 'Gestion des Nouvelles (Short Stories) — Admin AGAU Éditions')
@section('header-title', 'Récits & Chroniques')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Chroniques & Récits</h1>
            <p class="text-sm text-agau-muted font-medium">Anthologie de vos créations littéraires courtes.</p>
        </div>
        <a href="{{ route('admin.stories.create') }}" class="bg-agau-blue text-white px-6 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-journal-plus" style="font-size: 1.3rem"></i>
            Publier un récit
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-8 py-5">Œuvre & Ambiance</th>
                    <th class="px-8 py-5">Plume</th>
                    <th class="px-8 py-5">Thématiques</th>
                    <th class="px-8 py-5 text-center">Édition</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($stories as $story)
                    <tr class="hover:bg-agau-blue/5 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg shadow-sm border border-agau-border/20 shrink-0 group-hover:rotate-3 transition-transform relative overflow-hidden" style="background: {{ $story->gradient }}">
                                    @if($story->cover_path)
                                        <img src="{{ $story->cover_url }}" class="absolute inset-0 w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="font-bold text-agau-text group-hover:text-agau-blue transition-colors truncate max-w-[250px]">{{ $story->titre }}</span>
                                    <span class="text-[10px] text-agau-muted font-bold tracking-widest uppercase opacity-60">Récit court</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-bold text-agau-muted italic">{{ $story->author->nom ?? 'Auteur inconnu' }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-wrap gap-2">
                                @foreach($story->tags as $tag)
                                    <span class="bg-agau-blue/5 text-agau-blue-dark px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-tight border border-agau-blue/10">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex gap-3 justify-center">
                                <a href="{{ route('front.stories.show', $story->frontRouteParams()) }}" target="_blank" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Lire le récit">
                                    <i class="bi bi-eye" style="font-size: 1.3rem"></i>
                                </a>
                                <a href="{{ route('admin.stories.edit', $story) }}" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Éditer">
                                    <i class="bi bi-pencil-square" style="font-size: 1.3rem"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.stories.destroy', $story) }}" class="inline" onsubmit="return confirm('Retirer ce récit de la collection ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 hover:bg-red-50 rounded-full text-agau-muted hover:text-red-600 transition-all border-none bg-transparent cursor-pointer">
                                        <i class="bi bi-trash" style="font-size: 1.3rem"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <span class="material-symbols-outlined text-4xl text-agau-muted/20">book_2</span>
                                <p class="text-agau-muted font-lora italic">Aucune chronique n'a encore été contée.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- @if($stories->hasPages())
        <div class="mt-8">
            {{ $stories->links() }}
        </div>
    @endif -->
</div>
@endsection
