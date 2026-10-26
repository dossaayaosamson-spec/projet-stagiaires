@extends('layouts.admin')

@section('title', 'Nouvelle Œuvre — Admin AGAU Éditions')
@section('header-title', 'Création Littéraire')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.books.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
            <i class="bi bi-arrow-left-short text-[18px]"></i>
            Retour au Catalogue
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-8 border-b border-agau-border/20 bg-agau-border/5">
            <h2 class="font-lora text-2xl font-bold text-agau-text">Nouvel Ouvrage</h2>
            <p class="text-xs text-agau-muted font-medium mt-1">Enregistrement d'une nouvelle œuvre dans le patrimoine AGAU Éditions.</p>
        </div>

        <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-100 text-red-800 px-6 py-4 rounded-xl text-xs font-bold flex items-start gap-3">
                    <i class="bi bi-exclamation-triangle text-red-500 text-[20px]"></i>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Titre de l'Œuvre *</label>
                    <input type="text" name="titre" value="{{ old('titre') }}" required placeholder="Ex: L'Ombre du Baobab" 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Auteur Référent *</label>
                    <div class="relative">
                        <select name="author_id" id="author_select" required 
                            class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all appearance-none cursor-pointer">
                            <option value="">Sélectionner un auteur</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>{{ $author->nom }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-agau-muted pointer-events-none"></i>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Contrat d'édition associé (Optionnel)</label>
                    <div class="relative">
                        <select name="contract_id" 
                            class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all appearance-none cursor-pointer">
                            <option value="">Liaison automatique ou aucun contrat</option>
                            @foreach($unassignedContracts as $uc)
                                <option value="{{ $uc->id }}" {{ old('contract_id') == $uc->id ? 'selected' : '' }}>
                                    #{{ $uc->id }} — {{ $uc->author?->nom }} ({{ $uc->type_contrat === 'compte_editeur' ? 'Compte Éditeur' : 'Numérique' }} {{ $uc->taux_redevance }}%)
                                </option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-agau-muted pointer-events-none"></i>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Catégorie Littéraire *</label>
                    <input type="text" name="categorie" value="{{ old('categorie') }}" placeholder="Roman, Poésie, Essai..." required 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Maison d'Édition</label>
                    <input type="text" name="maison_edition" value="{{ old('maison_edition', 'AGAU Éditions') }}" placeholder="Ex: AGAU Éditions" 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>

                <div class="space-y-2" id="format-container">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Format (Numérique/Papier/...)</label>
                    <input type="text" name="format" id="format-input" value="{{ old('format', 'Numérique (PDF)') }}" placeholder="Ex: PDF, EPUB, Broché..." 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Prix (FCFA) *</label>
                    <input type="number" name="prix" value="{{ old('prix') }}" min="0" required placeholder="0" 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text font-bold focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Stock Physique (Ex: 50)</label>
                    <input type="number" name="stock" value="{{ old('stock', 10) }}" min="0" placeholder="0" 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text font-bold focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>
            </div>

            <div class="space-y-4">
                <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Disponibilité du Livre</label>
                <div class="flex items-center gap-10">
                    @foreach(['Numérique', 'Physique', 'Les deux'] as $disp)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="disponibilite" value="{{ $disp }}" {{ old('disponibilite', 'Numérique') == $disp ? 'checked' : '' }} class="hidden peer">
                            <div class="w-6 h-6 border-2 border-agau-border/40 rounded-full peer-checked:border-agau-blue flex items-center justify-center transition-all bg-white shadow-sm group-hover:border-agau-blue/30">
                                <div class="w-3.5 h-3.5 bg-agau-blue rounded-full scale-0 peer-checked:scale-100 transition-transform duration-200 ease-out"></div>
                            </div>
                            <span class="text-[13px] font-bold text-agau-muted peer-checked:text-agau-blue transition-colors uppercase tracking-wide">{{ $disp }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const radios = document.querySelectorAll('input[name="disponibilite"]');
                    const formatContainer = document.getElementById('format-container');
                    const formatInput = document.getElementById('format-input');

                    function updateFields(value) {
                        if (value === 'Physique') {
                            formatContainer.classList.add('hidden');
                            formatInput.required = false;
                        } else {
                            formatContainer.classList.remove('hidden');
                            formatInput.required = true;
                        }
                    }

                    radios.forEach(radio => {
                        radio.addEventListener('change', (e) => updateFields(e.target.value));
                    });

                    const checkedRadio = document.querySelector('input[name="disponibilite"]:checked');
                    if (checkedRadio) updateFields(checkedRadio.value);
                });
            </script>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Résumé de l'œuvre</label>
                <textarea name="resume" rows="4" placeholder="Une présentation captivante..." 
                    class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all resize-none">{{ old('resume') }}</textarea>
            </div>

            <div class="p-6 bg-agau-blue/5 rounded-2xl border border-agau-blue/10 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-agau-blue uppercase tracking-widest flex items-center gap-2">
                            <i class="bi bi-image"></i>
                            Couverture
                        </label>
                        <input type="file" name="cover_image" accept="image/*" 
                            class="text-xs text-agau-muted file:bg-white file:text-agau-blue file:border file:border-agau-blue/20 file:rounded-full file:px-4 file:py-1.5 file:mr-3 file:cursor-pointer file:font-bold hover:file:bg-agau-blue hover:file:text-white file:transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-agau-blue uppercase tracking-widest flex items-center gap-2">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Manuscrit (PDF)
                        </label>
                        <input type="file" name="pdf_file" accept=".pdf" 
                            class="text-xs text-agau-muted file:bg-white file:text-agau-blue file:border file:border-agau-blue/20 file:rounded-full file:px-4 file:py-1.5 file:mr-3 file:cursor-pointer file:font-bold hover:file:bg-agau-blue hover:file:text-white file:transition-all">
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-6 pt-6 border-t border-agau-blue/10">
                    <div class="flex items-center gap-8 flex-wrap">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" name="is_bestseller" id="is_bestseller" value="1" {{ old('is_bestseller') ? 'checked' : '' }} 
                                    class="peer hidden">
                                <div class="w-5 h-5 border-2 border-agau-blue/30 rounded-md peer-checked:bg-agau-blue peer-checked:border-agau-blue transition-all flex items-center justify-center">
                                    <i class="bi bi-check-lg text-white text-[14px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-agau-text group-hover:text-agau-blue transition-colors uppercase tracking-tight">Mise en avant (Bestseller)</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" name="is_free" id="is_free" value="1" {{ old('is_free') ? 'checked' : '' }} 
                                    class="peer hidden">
                                <div class="w-5 h-5 border-2 border-emerald-500/40 rounded-md peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all flex items-center justify-center">
                                    <i class="bi bi-check-lg text-white text-[14px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-emerald-700 group-hover:text-emerald-800 transition-colors uppercase tracking-tight">Livre Gratuit</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4 flex-wrap">
                        <div class="space-y-1">
                            <label class="text-[9px] font-bold text-agau-muted uppercase tracking-widest">Gratuit Jusqu'au (Optionnel)</label>
                            <input type="date" name="free_until" value="{{ old('free_until') }}" 
                                class="bg-white border border-agau-border/30 rounded-lg px-3 py-1.5 text-xs text-agau-text focus:outline-none focus:border-agau-blue">
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-agau-muted uppercase tracking-widest">Ambiance :</span>
                            <input type="color" name="couleur_couverture" value="{{ old('couleur_couverture', '#1B6CA8') }}" required 
                                class="w-8 h-8 border-none rounded-full cursor-pointer bg-transparent p-0 overflow-hidden shadow-sm hover:scale-110 transition-transform">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-agau-blue text-white py-4 rounded-xl text-xs font-bold tracking-[2px] uppercase cursor-pointer hover:bg-agau-blue-dark transition-all shadow-sm flex items-center justify-center gap-2">
                    <i class="bi bi-check-lg text-white text-[18px] mr-2"></i>
                    Publier l'Ouvrage
                </button>
                <a href="{{ route('admin.books.index') }}" class="px-8 py-4 bg-white border border-agau-border/40 text-agau-muted rounded-xl text-xs font-bold tracking-[2px] uppercase no-underline hover:bg-agau-border/5 transition-all">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
