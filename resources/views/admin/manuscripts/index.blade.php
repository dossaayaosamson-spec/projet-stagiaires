@extends('layouts.admin')

@section('title', 'Manuscrits reçus — Admin AGAU Éditions')
@section('header-title', 'Manuscrits reçus')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Manuscrits reçus</h1>
            <p class="text-sm text-agau-muted font-medium">Suivez les soumissions, filtrez par statut et consultez le dossier complet.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-agau-blue/10 text-agau-blue px-4 py-2 rounded-full text-xs font-bold">
                {{ $manuscripts->total() }} manuscrit{{ $manuscripts->total() > 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    <form method="GET" class="bg-white border border-agau-border/40 rounded-xl p-5 shadow-sm flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Recherche</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-agau-muted/60 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Titre, auteur, email..."
                    class="w-full pl-9 pr-4 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all">
            </div>
        </div>

        <div class="min-w-44">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Provenance</label>
            <select name="source" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all bg-white">
                <option value="">Toutes les provenances</option>
                <option value="candidature_auteur" {{ request('source') === 'candidature_auteur' ? 'selected' : '' }}>⭐ Candidature Auteur</option>
                <option value="lecthor_public" {{ request('source') === 'lecthor_public' ? 'selected' : '' }}>Lecthor Public</option>
                <option value="auteur_contrat" {{ request('source') === 'auteur_contrat' ? 'selected' : '' }}>Auteur sous contrat</option>
            </select>
        </div>

        <div class="min-w-40">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Statut</label>
            <select name="statut" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all bg-white">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ request('statut') === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="analyzed" {{ request('statut') === 'analyzed' ? 'selected' : '' }}>En cours d'analyse</option>
                <option value="accepted" {{ request('statut') === 'accepted' ? 'selected' : '' }}>Accepté</option>
                <option value="rejected" {{ request('statut') === 'rejected' ? 'selected' : '' }}>Refusé</option>
            </select>
        </div>

        <div class="min-w-40">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Catégorie</label>
            <select name="category" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all bg-white">
                <option value="">Toutes</option>
                @foreach($manuscripts->pluck('category')->filter()->unique()->sort() as $category)
                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-agau-blue text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-funnel"></i> Filtrer
        </button>

        @if(request()->hasAny(['search', 'source', 'statut', 'category']))
        <a href="{{ route('admin.manuscripts.index') }}" class="text-agau-muted text-sm hover:text-red-600 transition-colors flex items-center gap-1">
            <i class="bi bi-x-circle"></i> Réinitialiser
        </a>
        @endif
    </form>

    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-6 py-5">Auteur</th>
                    <th class="px-6 py-5">Titre</th>
                    <th class="px-6 py-5">Catégorie</th>
                    <th class="px-6 py-5">Statut</th>
                    <th class="px-6 py-5">Soumis le</th>
                    <th class="px-6 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($manuscripts as $manuscript)
                <tr class="hover:bg-agau-blue/5 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="font-bold text-agau-text group-hover:text-agau-blue transition-colors">
                            {{ $manuscript->pen_name ?: ($manuscript->author_name ?? ($manuscript->user?->nom_complet ?? 'Auteur inconnu')) }}
                        </div>
                        <div class="text-xs text-agau-muted mt-0.5">
                            {{ $manuscript->user?->email ?? 'Email non renseigné' }}
                        </div>
                        @if($manuscript->submission_source === 'candidature_auteur')
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full mt-1">
                                <i class="bi bi-star-fill text-[9px]"></i> Candidature Auteur
                            </span>
                        @elseif($manuscript->submission_source === 'auteur_contrat')
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full mt-1">
                                <i class="bi bi-file-earmark-check"></i> Sous contrat
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full mt-1">
                                <i class="bi bi-robot"></i> Lecthor Public
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-agau-text">{{ $manuscript->title ?? 'Sans titre' }}</span>
                        @if($manuscript->contract)
                            <div class="text-[10px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                                <i class="bi bi-check-circle-fill"></i> Contrat {{ $manuscript->contract->taux_redevance }}%
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                            {{ $manuscript->category ?? 'Non précisée' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusMap = [
                                'pending' => ['bg-amber-100 text-amber-700', 'clock', 'En attente'],
                                'analyzed' => ['bg-blue-100 text-blue-700', 'arrow-repeat', 'En cours d\'analyse'],
                                'accepted' => ['bg-green-100 text-green-700', 'check-circle', 'Accepté'],
                                'rejected' => ['bg-red-100 text-red-700', 'x-circle', 'Refusé'],
                            ];
                            [$classes, $icon, $label] = $statusMap[$manuscript->status] ?? ['bg-gray-100 text-gray-700', 'question', ucfirst($manuscript->status ?? 'Inconnu')];
                        @endphp
                        <span class="{{ $classes }} px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight inline-flex items-center gap-1">
                            <i class="bi bi-{{ $icon }}"></i> {{ $label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-lora text-agau-muted text-xs">
                        {{ $manuscript->submitted_at ? $manuscript->submitted_at->translatedFormat('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('admin.manuscripts.show', $manuscript) }}"
                                class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Voir le dossier">
                                <i class="bi bi-eye" style="font-size: 1.2rem;"></i>
                            </a>
                            <a href="{{ route('admin.manuscripts.download', $manuscript) }}"
                                class="p-2 hover:bg-green-50 rounded-full text-agau-muted hover:text-green-600 transition-all" title="Télécharger le manuscrit">
                                <i class="bi bi-cloud-download" style="font-size: 1.2rem;"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.manuscripts.destroy', $manuscript) }}" onsubmit="return confirm('Supprimer définitivement ce manuscrit ?')">
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
                            <p class="text-agau-muted font-lora italic">Aucun manuscrit trouvé.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($manuscripts->hasPages())
        <div class="mt-6">
            {{ $manuscripts->links() }}
        </div>
    @endif
</div>
@endsection
