@extends('layouts.admin')

@section('title', 'Contrats d\'édition — Admin AGAU Éditions')
@section('header-title', 'Contrats d\'édition')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Contrats d'édition</h1>
            <p class="text-sm text-agau-muted font-medium">Gérez les conventions éditoriales, types de contrat et taux de royalties par ouvrage.</p>
        </div>
        <div>
            <a href="{{ route('admin.contracts.create') }}" class="bg-agau-blue text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm inline-flex items-center gap-2 no-underline">
                <i class="bi bi-plus-lg"></i> Nouveau Contrat
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <form method="GET" class="bg-white border border-agau-border/40 rounded-xl p-5 shadow-sm flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Recherche</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-agau-muted/60 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nom d'auteur ou titre du livre..."
                    class="w-full pl-9 pr-4 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all">
            </div>
        </div>

        <div class="min-w-44">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Type de contrat</label>
            <select name="type_contrat" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm bg-white">
                <option value="">Tous les types</option>
                <option value="compte_editeur" {{ request('type_contrat') === 'compte_editeur' ? 'selected' : '' }}>Compte d'éditeur</option>
                <option value="distribution_numerique" {{ request('type_contrat') === 'distribution_numerique' ? 'selected' : '' }}>Distribution Numérique</option>
                <option value="autre" {{ request('type_contrat') === 'autre' ? 'selected' : '' }}>Autre</option>
            </select>
        </div>

        <div class="min-w-40">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Statut</label>
            <select name="statut" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm bg-white">
                <option value="">Tous les statuts</option>
                <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="en_attente_signature" {{ request('statut') === 'en_attente_signature' ? 'selected' : '' }}>En attente signature</option>
                <option value="brouillon" {{ request('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                <option value="resilie" {{ request('statut') === 'resilie' ? 'selected' : '' }}>Résilié</option>
            </select>
        </div>

        <button type="submit" class="bg-agau-blue text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-funnel"></i> Filtrer
        </button>

        @if(request()->hasAny(['search', 'type_contrat', 'statut']))
        <a href="{{ route('admin.contracts.index') }}" class="text-agau-muted text-sm hover:text-red-600 transition-colors flex items-center gap-1">
            <i class="bi bi-x-circle"></i> Réinitialiser
        </a>
        @endif
    </form>

    <!-- Tableau -->
    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-6 py-5">Auteur</th>
                    <th class="px-6 py-5">Livre / Manuscrit</th>
                    <th class="px-6 py-5">Type de contrat</th>
                    <th class="px-6 py-5">Redevance (%)</th>
                    <th class="px-6 py-5">Statut</th>
                    <th class="px-6 py-5">Date début</th>
                    <th class="px-6 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($contracts as $contract)
                <tr class="hover:bg-agau-blue/5 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-agau-text">{{ $contract->author?->nom ?? 'Auteur inconnu' }}</div>
                        <div class="text-xs text-agau-muted">{{ $contract->author?->user?->email ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($contract->book)
                            <div class="font-semibold text-agau-text flex items-center gap-1.5">
                                <i class="bi bi-book text-agau-blue"></i> {{ $contract->book->titre }}
                            </div>
                        @elseif($contract->manuscript)
                            <div class="text-xs text-agau-muted italic flex items-center gap-1.5">
                                <i class="bi bi-file-text"></i> Manuscrit : {{ $contract->manuscript->title }}
                            </div>
                        @else
                            <span class="text-agau-muted text-xs">Non rattaché</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($contract->type_contrat === 'compte_editeur')
                            <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                                Compte d'éditeur
                            </span>
                        @elseif($contract->type_contrat === 'distribution_numerique')
                            <span class="bg-purple-50 text-purple-700 border border-purple-200 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                                Distribution Numérique
                            </span>
                        @else
                            <span class="bg-gray-50 text-gray-700 border border-gray-200 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                                {{ ucfirst($contract->type_contrat) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-bold text-agau-blue">
                        {{ number_format($contract->taux_redevance, 1) }} %
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusBadge = match($contract->statut) {
                                'actif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'en_attente_signature' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'resilie' => 'bg-red-50 text-red-700 border-red-200',
                                default => 'bg-gray-50 text-gray-700 border-gray-200',
                            };
                        @endphp
                        <span class="{{ $statusBadge }} border px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                            {{ str_replace('_', ' ', $contract->statut) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-agau-muted font-lora">
                        {{ $contract->date_debut ? $contract->date_debut->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            @if($contract->fichier_contrat_pdf)
                            <a href="{{ route('admin.contracts.download', $contract) }}" class="p-2 hover:bg-agau-blue/10 rounded-full text-agau-blue transition-all" title="Télécharger le PDF">
                                <i class="bi bi-file-earmark-pdf" style="font-size: 1.1rem;"></i>
                            </a>
                            @endif
                            <a href="{{ route('admin.contracts.edit', $contract) }}" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Modifier">
                                <i class="bi bi-pencil" style="font-size: 1.1rem;"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.contracts.destroy', $contract) }}" onsubmit="return confirm('Confirmer la suppression de ce contrat ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-50 rounded-full text-agau-muted hover:text-red-600 transition-all border-none bg-transparent cursor-pointer" title="Supprimer">
                                    <i class="bi bi-trash" style="font-size: 1.1rem;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-8 py-16 text-center text-agau-muted font-lora italic">
                        Aucun contrat enregistré pour le moment.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $contracts->links() }}
    </div>
</div>
@endsection
