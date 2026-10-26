@extends('layouts.admin')

@section('title', 'Édition : ' . $story->titre . ' — Admin AGAU Éditions')
@section('header-title', 'Plume Narrative')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.stories.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
            <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Retour à l'Anthologie
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-8 border-b border-agau-border/20 bg-agau-border/5">
            <h2 class="font-lora text-2xl font-bold text-agau-text">Modifier le Récit</h2>
            <p class="text-xs text-agau-muted font-medium mt-1">Perfectionnement de <span class="text-agau-blue font-bold">"{{ $story->titre }}"</span> pour vos lecteurs.</p>
        </div>

        <form method="POST" action="{{ route('admin.stories.update', $story) }}" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 border border-red-100 text-red-800 px-6 py-4 rounded-xl text-xs font-bold flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-500 text-[20px]">error</span>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Titre du Récit *</label>
                    <input type="text" name="titre" value="{{ old('titre', $story->titre) }}" required 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Plume Narrative *</label>
                    <div class="relative">
                        <select name="author_id" required 
                            class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all appearance-none cursor-pointer">
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ old('author_id', $story->author_id) == $author->id ? 'selected' : '' }}>{{ $author->nom }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-agau-muted pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Thématiques (Tags)</label>
                    <input type="text" name="tags" value="{{ old('tags', is_array($story->tags) ? implode(', ', $story->tags) : $story->tags) }}" placeholder="Vie, Espoir, Voyage..." 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Image de Couverture</label>
                    
                    @if($story->cover_path)
                        <div class="mb-4">
                            <p class="text-[9px] text-agau-muted uppercase tracking-widest mb-2 font-bold">Couverture actuelle :</p>
                            <img src="{{ $story->cover_url }}" alt="Couverture" class="w-32 h-44 object-cover rounded-lg border border-agau-border/30 shadow-sm">
                        </div>
                    @endif

                    <input type="file" name="cover_image" accept="image/*" 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-agau-blue file:text-white hover:file:bg-agau-blue-dark">
                    <p class="text-[9px] text-agau-muted font-medium italic mt-1">Laissez vide pour conserver la couverture actuelle.</p>
                </div>
            </div>

            <div class="p-6 bg-green-50/50 border border-green-100 rounded-2xl space-y-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_free" value="1" {{ old('is_free', $story->is_free) ? 'checked' : '' }} class="w-5 h-5 text-agau-blue rounded border-agau-border/30 focus:ring-agau-blue">
                    <span class="text-sm font-bold text-agau-text">Récit Gratuit (Nécessite uniquement un compte)</span>
                </label>
                <p class="text-[10px] text-agau-muted font-medium italic ml-8">Si coché, les lecteurs n'auront pas à payer (via FedaPay) pour lire ce récit. Ils devront simplement se connecter.</p>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Composition du Récit *</label>
                <textarea name="extrait" rows="8" required 
                    class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all resize-none shadow-inner leading-relaxed">{{ old('extrait', $story->extrait) }}</textarea>
            </div>

            <div class="p-6 bg-agau-blue/5 rounded-2xl border border-agau-blue/10 space-y-4">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-agau-blue uppercase tracking-widest block">Signature Visuelle</span>
                    <span class="text-[10px] text-agau-muted font-medium italic">Gradient d'ambiance pour l'interface publique</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl shadow-sm border-2 border-white shrink-0" style="background: {{ $story->gradient }}"></div>
                    <input type="text" name="gradient" value="{{ old('gradient', $story->gradient) }}" required 
                        class="flex-1 bg-white border border-agau-blue/10 rounded-xl px-4 py-2.5 text-xs text-agau-text font-mono focus:outline-none focus:ring-1 focus:ring-agau-blue/30 transition-all">
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-agau-blue text-white py-4 rounded-xl text-xs font-bold tracking-[2px] uppercase cursor-pointer hover:bg-agau-blue-dark transition-all shadow-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Enregistrer les modifications
                </button>
                <a href="{{ route('admin.stories.index') }}" class="px-8 py-4 bg-white border border-agau-border/40 text-agau-muted rounded-xl text-xs font-bold tracking-[2px] uppercase no-underline hover:bg-agau-border/5 transition-all">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
