@extends('layouts.admin')

@section('title', 'Candidatures ECLA — Admin AGAU Éditions')
@section('header-title', 'Concours eCLA 2026')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Candidatures eCLA</h1>
            <p class="text-sm text-agau-muted font-medium">Gérez les dossiers soumis au concours eCLA 2026.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-agau-blue/10 text-agau-blue px-4 py-2 rounded-full text-xs font-bold">
                {{ $candidatures->total() }} dossier{{ $candidatures->total() > 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="bg-white border border-agau-border/40 rounded-xl p-5 shadow-sm flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Rechercher</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-agau-muted/60 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nom, prénom, titre..."
                    class="w-full pl-9 pr-4 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all">
            </div>
        </div>

        <div class="min-w-40">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Statut</label>
            <select name="statut" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all bg-white">
                <option value="">Tous les statuts</option>
                <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="acceptee" {{ request('statut') === 'acceptee' ? 'selected' : '' }}>Acceptée</option>
                <option value="refusee" {{ request('statut') === 'refusee' ? 'selected' : '' }}>Refusée</option>
            </select>
        </div>

        <div class="min-w-40">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Genre</label>
            <select name="genre_litteraire" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all bg-white">
                <option value="">Tous les genres</option>
                <option value="Roman" {{ request('genre_litteraire') === 'Roman' ? 'selected' : '' }}>Roman</option>
                <option value="Nouvelle" {{ request('genre_litteraire') === 'Nouvelle' ? 'selected' : '' }}>Nouvelle</option>
            </select>
        </div>

        <button type="submit" class="bg-agau-blue text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-funnel"></i> Filtrer
        </button>

        @if(request()->hasAny(['search', 'statut', 'genre_litteraire']))
        <a href="{{ route('admin.candidatures.index') }}" class="text-agau-muted text-sm hover:text-red-600 transition-colors flex items-center gap-1">
            <i class="bi bi-x-circle"></i> Réinitialiser
        </a>
        @endif
    </form>

    {{-- Flash --}}
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3 text-sm text-green-700">
        <i class="bi bi-check-circle-fill text-green-500 text-base"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-6 py-5">Candidat(e)</th>
                    <th class="px-6 py-5">Œuvre</th>
                    <th class="px-6 py-5">Genre</th>
                    <th class="px-6 py-5">Statut</th>
                    <th class="px-6 py-5">Date</th>
                    <th class="px-6 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($candidatures as $candidature)
                <tr class="hover:bg-agau-blue/5 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="font-bold text-agau-text group-hover:text-agau-blue transition-colors">
                            {{ $candidature->nom }} {{ $candidature->prenoms }}
                        </div>
                        <div class="text-xs text-agau-muted mt-0.5">{{ $candidature->email }}</div>
                        <div class="text-xs text-agau-muted">{{ $candidature->pays_residence }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-agau-text">{{ $candidature->titre_manuscrit }}</span>
                        <div class="text-xs text-agau-muted mt-0.5">{{ number_format($candidature->nombre_mots) }} mots</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                            {{ $candidature->genre_litteraire }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusMap = [
                                'en_attente' => ['bg-amber-100 text-amber-700', 'clock', 'En attente'],
                                'en_cours'   => ['bg-blue-100 text-blue-700', 'arrow-clockwise', 'En cours'],
                                'acceptee'   => ['bg-green-100 text-green-700', 'check-circle', 'Acceptée'],
                                'refusee'    => ['bg-red-100 text-red-700', 'x-circle', 'Refusée'],
                            ];
                            [$classes, $icon, $label] = $statusMap[$candidature->statut] ?? ['bg-gray-100 text-gray-700', 'question', $candidature->statut];
                        @endphp
                        <span class="{{ $classes }} px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight inline-flex items-center gap-1">
                            <i class="bi bi-{{ $icon }}"></i> {{ $label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-lora text-agau-muted text-xs">
                        {{ $candidature->created_at->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('admin.candidatures.show', $candidature) }}"
                                class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Voir le dossier">
                                <i class="bi bi-eye" style="font-size: 1.2rem;"></i>
                            </a>
                            <a href="{{ route('admin.candidatures.download', $candidature) }}"
                                class="p-2 hover:bg-green-50 rounded-full text-agau-muted hover:text-green-600 transition-all" title="Télécharger le manuscrit">
                                <i class="bi bi-cloud-download" style="font-size: 1.2rem;"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.candidatures.destroy', $candidature) }}" onsubmit="return confirm('Supprimer définitivement cette candidature ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-50 rounded-full text-agau-muted hover:text-red-600 transition-all border-none bg-transparent cursor-pointer" title="Supprimer">
                                    <i class="bi bi-trash" style="font-size: 1.2rem;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <i class="bi bi-inbox text-4xl text-agau-muted/20"></i>
                            <p class="text-agau-muted font-lora italic">Aucune candidature trouvée.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($candidatures->hasPages())
        <div class="mt-6">
            {{ $candidatures->links() }}
        </div>
    @endif
</div>
@endsection
