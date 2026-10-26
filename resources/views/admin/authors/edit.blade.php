@extends('layouts.admin')

@section('title', 'Édition : ' . $author->nom . ' — Admin AGAU Éditions')
@section('header-title', 'Profil de Plume')

@section('content')
    <div class="max-w-3xl mx-auto space-y-8">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.authors.index') }}"
                class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
                <i class="bi bi-arrow-left-short text-[18px]"></i>
                Retour au Cercle
            </a>
        </div>

        <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-8 border-b border-agau-border/20 bg-agau-border/5 flex items-center gap-6">
                @if($author->avatar)
                    <img src="{{ asset('storage/' . $author->avatar) }}" alt="{{ $author->nom }}"
                        class="w-16 h-16 rounded-2xl shadow-sm object-cover border-2 border-white">
                @else
                    <div class="w-16 h-16 rounded-2xl shadow-sm flex items-center justify-center text-white font-bold text-2xl shrink-0"
                        style="background: {{ $author->couleur_avatar }}">
                        {{ $author->initiales }}
                    </div>
                @endif
                <div>
                    <h2 class="font-lora text-2xl font-bold text-agau-text">Modifier l'Auteur</h2>
                    <p class="text-xs text-agau-muted font-medium mt-1">Mise à jour du profil de <span
                            class="text-agau-blue font-bold">{{ $author->nom }}</span>.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.authors.update', $author) }}" enctype="multipart/form-data"
                class="p-8 space-y-8">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div
                        class="bg-red-50 border border-red-100 text-red-800 px-6 py-4 rounded-xl text-xs font-bold flex items-start gap-3">
                        <i class="bi bi-exclamation-triangle text-red-500 text-[20px]"></i>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Nom Complet /
                            Plume *</label>
                        <input type="text" name="nom" value="{{ old('nom', $author->nom) }}" required
                            class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Changer
                            l'Avatar</label>
                        <input type="file" name="avatar" accept="image/*"
                            class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-2.5 text-xs text-agau-text file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-agau-blue/10 file:text-agau-blue hover:file:bg-agau-blue/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Initiales
                            (Optionnel)</label>
                        <input type="text" name="initiales" value="{{ old('initiales', $author->initiales) }}" maxlength="4"
                            placeholder="Ex: JR"
                            class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text font-bold uppercase focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                        <p class="text-[9px] text-agau-muted italic mt-1">Générées automatiquement si vides.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Ouvrages
                            publiés</label>
                        <input type="number" name="nb_livres" value="{{ old('nb_livres', $author->nb_livres) }}" min="0"
                            class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Biographie</label>
                    <textarea name="bio" rows="5"
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all resize-none">{{ old('bio', $author->bio) }}</textarea>
                </div>

                <div class="p-6 bg-agau-blue/5 rounded-2xl border border-agau-blue/10 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-agau-blue uppercase tracking-widest block">Identité
                            visuelle</span>
                        <span class="text-[10px] text-agau-muted font-medium italic">Ajustez la couleur de signature</span>
                    </div>
                    <input type="color" name="couleur_avatar" value="{{ old('couleur_avatar', $author->couleur_avatar) }}"
                        required
                        class="w-10 h-10 border border-agau-blue rounded-full cursor-pointer bg-transparent p-0 overflow-hidden shadow-sm transition-transform">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-agau-blue text-white py-4 rounded-xl text-xs font-bold tracking-[2px] uppercase cursor-pointer hover:bg-agau-blue-dark transition-all shadow-sm flex items-center justify-center gap-2">
                        <i class="bi bi-check2-circle text-[15px]"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('admin.authors.index') }}"
                        class="px-8 py-4 bg-white border border-agau-border/40 text-agau-muted rounded-xl text-xs font-bold tracking-[2px] uppercase no-underline hover:bg-agau-border/5 transition-all">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection