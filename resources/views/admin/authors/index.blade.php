@extends('layouts.admin')

@section('title', 'Gestion des Auteurs — Admin AGAU Éditions')
@section('header-title', 'Cercle des Auteurs')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <div>
                <h1 class="font-lora text-3xl font-bold text-agau-text">Cercle des Auteurs</h1>
                <p class="text-sm text-agau-muted font-medium">Gestion de vos plumes et contributeurs.</p>
            </div>
            <a href="{{ route('admin.authors.create') }}"
                class="bg-agau-blue text-white px-6 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-sm flex items-center gap-2">
                <i class="bi bi-person-add" style="font-size: 1.3rem"></i>
                Recruter un auteur
            </a>
        </div>

        <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                        <th class="px-8 py-5">Identité</th>
                        <th class="px-8 py-5 text-center">Ouvrages</th>
                        <th class="px-8 py-5">Membre depuis</th>
                        <th class="px-8 py-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                    @forelse($authors as $author)
                        <tr class="hover:bg-agau-blue/5 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-[12px] shadow-sm transform group-hover:scale-110 transition-transform"
                                        style="background: {{ $author->couleur_avatar }}">
                                        {{ $author->initiales }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-agau-text group-hover:text-agau-blue transition-colors">{{ $author->nom }}</span>
                                        <span
                                            class="text-[10px] text-agau-muted font-bold uppercase tracking-widest opacity-60">Actif</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span
                                    class="bg-agau-border/10 text-agau-blue-dark px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                                    {{ $author->nb_livres }} livres
                                </span>
                            </td>
                            <td class="px-8 py-5 text-agau-muted font-medium italic text-xs">
                                {{ $author->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex gap-3 justify-center">
                                    <a href="{{ route('front.auteurs.show', $author->frontRouteParams()) }}" target="_blank"
                                        class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all"
                                        title="Voir sur le site">
                                        <i class="bi bi-eye" style="font-size: 1.3rem"></i>
                                    </a>
                                    <a href="{{ route('admin.authors.edit', $author) }}"
                                        class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all"
                                        title="Éditer">
                                        <i class="bi bi-pencil-square" style="font-size: 1.3rem"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.authors.destroy', $author) }}" class="inline"
                                        onsubmit="return confirm('Retirer définitivement cet auteur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 hover:bg-red-50 rounded-full text-agau-muted hover:text-red-600 transition-all border-none bg-transparent cursor-pointer">
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
                                    <i class="bi bi-person-x" style="font-size: 1.3rem"></i>
                                    <p class="text-agau-muted font-lora italic">Le cercle n'a pas encore accueilli de plumes.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($authors->hasPages())
            <div class="mt-8">
                {{ $authors->links() }}
            </div>
        @endif
    </div>
@endsection