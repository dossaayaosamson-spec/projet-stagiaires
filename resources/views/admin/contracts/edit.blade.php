@extends('layouts.admin')

@section('title', 'Modifier le Contrat — Admin AGAU Éditions')
@section('header-title', 'Modifier le Contrat d\'édition')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.contracts.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
            <i class="bi bi-arrow-left text-[18px] group-hover:-translate-x-1 transition-transform"></i>
            Retour aux contrats
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm p-8">
        <h2 class="font-lora text-2xl font-bold text-agau-text mb-6">Modifier le contrat #{{ $contract->id }}</h2>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
            <ul class="list-disc pl-5 m-0 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.contracts.update', $contract) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Auteur concerné *</label>
                    <select name="author_id" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" {{ old('author_id', $contract->author_id) == $author->id ? 'selected' : '' }}>
                                {{ $author->nom }} ({{ $author->user?->email ?? 'Sans compte' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Livre associé</label>
                    <select name="book_id" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white">
                        <option value="">Livre non encore publié</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" {{ old('book_id', $contract->book_id) == $book->id ? 'selected' : '' }}>
                                {{ $book->titre }} ({{ $book->author?->nom }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Manuscrit d'origine</label>
                    <select name="manuscript_id" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white">
                        <option value="">Aucun manuscrit lié</option>
                        @foreach($manuscripts as $manuscript)
                            <option value="{{ $manuscript->id }}" {{ old('manuscript_id', $contract->manuscript_id) == $manuscript->id ? 'selected' : '' }}>
                                {{ $manuscript->title }} — {{ $manuscript->author_name ?? $manuscript->pen_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Type de contrat *</label>
                    <select name="type_contrat" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required>
                        <option value="compte_editeur" {{ old('type_contrat', $contract->type_contrat) === 'compte_editeur' ? 'selected' : '' }}>Compte d'éditeur (Classique)</option>
                        <option value="distribution_numerique" {{ old('type_contrat', $contract->type_contrat) === 'distribution_numerique' ? 'selected' : '' }}>Distribution Numérique / Coédition</option>
                        <option value="autre" {{ old('type_contrat', $contract->type_contrat) === 'autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Taux de Redevance (%) *</label>
                    <input type="number" step="0.1" min="0" max="100" name="taux_redevance" value="{{ old('taux_redevance', $contract->taux_redevance) }}" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Statut *</label>
                    <select name="statut" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required>
                        <option value="actif" {{ old('statut', $contract->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="en_attente_signature" {{ old('statut', $contract->statut) === 'en_attente_signature' ? 'selected' : '' }}>En attente de signature</option>
                        <option value="brouillon" {{ old('statut', $contract->statut) === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="resilie" {{ old('statut', $contract->statut) === 'resilie' ? 'selected' : '' }}>Résilié</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Date d'effet (Début) *</label>
                    <input type="date" name="date_debut" value="{{ old('date_debut', $contract->date_debut ? $contract->date_debut->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white" required>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Date d'échéance</label>
                    <input type="date" name="date_fin" value="{{ old('date_fin', $contract->date_fin ? $contract->date_fin->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-agau-muted mb-2">Document PDF du contrat signé</label>
                    @if($contract->fichier_contrat_pdf)
                    <div class="flex items-center gap-2 mb-2 p-3 bg-agau-border/5 rounded-xl border border-agau-border/20 text-xs">
                        <i class="bi bi-file-earmark-pdf text-red-500 text-lg"></i>
                        <span class="text-agau-text font-medium">Document actuellement enregistré</span>
                        <a href="{{ route('admin.contracts.download', $contract) }}" class="text-agau-blue underline ml-auto">Télécharger</a>
                    </div>
                    @endif
                    <input type="file" name="fichier_contrat_pdf" accept="application/pdf" class="w-full px-4 py-2.5 border border-agau-border/50 rounded-xl text-sm bg-white">
                    <p class="text-[11px] text-agau-muted mt-1">Laissez vide pour conserver le document actuel.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-agau-border/20">
                <a href="{{ route('admin.contracts.index') }}" class="px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-agau-muted hover:text-agau-text no-underline">
                    Annuler
                </a>
                <button type="submit" class="bg-agau-blue text-white px-6 py-2.5 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
