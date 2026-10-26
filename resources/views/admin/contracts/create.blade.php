@extends('layouts.admin')

@section('title', 'Nouveau Contrat — Admin AGAU Éditions')
@section('header-title', 'Nouveau Contrat d\'édition')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.contracts.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
            <i class="bi bi-arrow-left text-[18px] group-hover:-translate-x-1 transition-transform"></i>
            Retour aux contrats
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm p-8">
        <h2 class="font-lora text-2xl font-bold text-agau-text mb-6">Créer une convention d'édition</h2>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
            <ul class="list-disc pl-5 m-0 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.contracts.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Auteur concerné *</label>
                    <select name="author_id" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required>
                        <option value="">Sélectionner un auteur</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                {{ $author->nom }} ({{ $author->user?->email ?? 'Sans compte' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Livre associé (Optionnel)</label>
                    <select name="book_id" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white">
                        <option value="">Livre non encore publié</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                {{ $book->titre }} ({{ $book->author?->nom }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Manuscrit d'origine (Optionnel)</label>
                    <select name="manuscript_id" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white">
                        <option value="">Aucun manuscrit lié</option>
                        @foreach($manuscripts as $manuscript)
                            <option value="{{ $manuscript->id }}" {{ old('manuscript_id') == $manuscript->id ? 'selected' : '' }}>
                                {{ $manuscript->title }} — {{ $manuscript->author_name ?? $manuscript->pen_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Type de contrat *</label>
                    <select name="type_contrat" id="type_contrat" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required onchange="handleTypeChange(this.value)">
                        <option value="compte_editeur" {{ old('type_contrat') === 'compte_editeur' ? 'selected' : '' }}>Compte d'éditeur (Classique)</option>
                        <option value="distribution_numerique" {{ old('type_contrat') === 'distribution_numerique' ? 'selected' : '' }}>Distribution Numérique / Coédition</option>
                        <option value="autre" {{ old('type_contrat') === 'autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Taux de Redevance (%) *</label>
                    <input type="number" step="0.1" min="0" max="100" name="taux_redevance" id="taux_redevance" value="{{ old('taux_redevance', '10.0') }}" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required>
                    <p class="text-[11px] text-agau-muted mt-1">Exemple : 10% compte d'éditeur, 50% numérique.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Statut *</label>
                    <select name="statut" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required>
                        <option value="actif" {{ old('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="en_attente_signature" {{ old('statut') === 'en_attente_signature' ? 'selected' : '' }}>En attente de signature</option>
                        <option value="brouillon" {{ old('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="resilie" {{ old('statut') === 'resilie' ? 'selected' : '' }}>Résilié</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Date d'effet (Début) *</label>
                    <input type="date" name="date_debut" value="{{ old('date_debut', now()->toDateString()) }}" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Date d'échéance (Optionnel)</label>
                    <input type="date" name="date_fin" value="{{ old('date_fin') }}" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Document PDF du contrat signé (Optionnel)</label>
                    <input type="file" name="fichier_contrat_pdf" accept="application/pdf" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-agau-border/20">
                <a href="{{ route('admin.contracts.index') }}" class="px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-agau-muted hover:text-agau-text no-underline">
                    Annuler
                </a>
                <button type="submit" class="bg-agau-blue text-white px-6 py-2.5 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm">
                    Créer le contrat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function handleTypeChange(type) {
        const rate = document.getElementById('taux_redevance');
        if (type === 'compte_editeur' && (rate.value === '50' || rate.value === '50.0' || !rate.value)) {
            rate.value = '10.0';
        } else if (type === 'distribution_numerique' && (rate.value === '10' || rate.value === '10.0' || !rate.value)) {
            rate.value = '50.0';
        }
    }
</script>
@endsection
