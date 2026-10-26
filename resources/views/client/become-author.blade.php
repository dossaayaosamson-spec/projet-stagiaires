@extends('layouts.dashboard')

@section('title', 'Candidature Auteur — AGAU Éditions')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="border-b border-agau-border/40 pb-6">
        <div class="flex items-center gap-2 text-agau-blue text-xs font-bold uppercase tracking-widest mb-2">
            <i class="bi bi-feather"></i>
            <span>Rejoindre la Maison d'Édition</span>
        </div>
        <h1 class="font-serif text-3xl md:text-4xl font-bold text-agau-text">
            Postuler en tant qu'<span class="text-agau-blue">Auteur</span>
        </h1>
        <p class="text-sm text-agau-muted mt-2 max-w-2xl leading-relaxed">
            Vous portez une voix singulière et un projet littéraire abouti ? Déposez votre candidature auprès de notre comité éditorial. Si votre manuscrit est retenu, un contrat d'édition vous sera proposé et votre espace auteur officiel sera débloqué.
        </p>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl text-sm space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i class="bi bi-info-circle-fill"></i>
                <span>Veuillez corriger les champs suivants :</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.become-author.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Section 1 : Profil Littéraire -->
        <div class="bg-white border border-agau-border/50 rounded-2xl p-6 md:p-8 shadow-sm space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-agau-border/30">
                <div class="w-10 h-10 rounded-full bg-agau-blue/10 text-agau-blue flex items-center justify-center font-bold text-sm">
                    1
                </div>
                <div>
                    <h2 class="font-serif text-lg font-bold text-agau-text m-0">Profil & Identité Littéraire</h2>
                    <p class="text-xs text-agau-muted m-0">Comment souhaitez-vous être présenté auprès de nos lecteurs et du comité ?</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom de plume / Alias -->
                <div>
                    <label for="pen_name" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                        Nom de plume / Alias littéraire <span class="text-agau-muted font-normal">(Optionnel)</span>
                    </label>
                    <input type="text" name="pen_name" id="pen_name" value="{{ old('pen_name', $user->alias) }}"
                        placeholder="Ex : Émile Zola, Reine d'Afrique..."
                        class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-agau-blue focus:ring-1 focus:ring-agau-blue transition-all @error('pen_name') border-red-400 @enderror">
                    <p class="text-[11px] text-agau-muted mt-1">Si laissé vide, votre nom civil ({{ $user->prenom }} {{ $user->nom_famille }}) sera utilisé.</p>
                </div>

                <!-- Portrait / Photo d'auteur -->
                <div>
                    <label for="portrait" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                        Portrait / Photo d'auteur <span class="text-agau-muted font-normal">(Optionnel)</span>
                    </label>
                    <input type="file" name="portrait" id="portrait" accept="image/jpeg,image/png,image/webp"
                        class="w-full text-xs text-agau-muted file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-agau-blue/10 file:text-agau-blue hover:file:bg-agau-blue/20 file:cursor-pointer transition-all @error('portrait') border-red-400 @enderror">
                    <p class="text-[11px] text-agau-muted mt-1">Formats acceptés : JPG, PNG, WEBP (Max : 2 Mo).</p>
                </div>

                <!-- Pays -->
                <div>
                    <label for="country" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                        Pays de résidence <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="country" id="country" value="{{ old('country', $user->pays ?? 'Togo') }}" required
                        placeholder="Ex : Togo, Bénin, Côte d'Ivoire, France..."
                        class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-agau-blue focus:ring-1 focus:ring-agau-blue transition-all @error('country') border-red-400 @enderror">
                </div>

                <!-- Ville -->
                <div>
                    <label for="city" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                        Ville de résidence <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}" required
                        placeholder="Ex : Lomé, Cotonou, Abidjan, Paris..."
                        class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-agau-blue focus:ring-1 focus:ring-agau-blue transition-all @error('city') border-red-400 @enderror">
                </div>
            </div>

            <!-- Liens réseaux / site -->
            <div>
                <label for="social_links" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                    Site web ou Profil social <span class="text-agau-muted font-normal">(Optionnel)</span>
                </label>
                <input type="url" name="social_links" id="social_links" value="{{ old('social_links') }}"
                    placeholder="https://instagram.com/monprofil ou https://monsite.com"
                    class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-agau-blue focus:ring-1 focus:ring-agau-blue transition-all @error('social_links') border-red-400 @enderror">
            </div>

            <!-- Biographie -->
            <div>
                <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                    Notice biographique & Parcours <span class="text-red-500">*</span>
                </label>
                <textarea name="bio" id="bio" rows="4" required minlength="30" maxlength="3000"
                    placeholder="Présentez votre parcours, vos influences littéraires, vos publications antérieures si vous en avez..."
                    class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl p-4 text-sm focus:bg-white focus:border-agau-blue focus:ring-1 focus:ring-agau-blue transition-all @error('bio') border-red-400 @enderror">{{ old('bio') }}</textarea>
                <p class="text-[11px] text-agau-muted mt-1">Minimum 30 caractères. Sera affiché sur votre page auteur publique si votre contrat est validé.</p>
            </div>
        </div>

        <!-- Section 2 : Le Projet Littéraire -->
        <div class="bg-white border border-agau-border/50 rounded-2xl p-6 md:p-8 shadow-sm space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-agau-border/30">
                <div class="w-10 h-10 rounded-full bg-agau-blue/10 text-agau-blue flex items-center justify-center font-bold text-sm">
                    2
                </div>
                <div>
                    <h2 class="font-serif text-lg font-bold text-agau-text m-0">Le Projet Littéraire Soumis</h2>
                    <p class="text-xs text-agau-muted m-0">Présentez l'ouvrage que vous souhaitez publier avec AGAU Éditions.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Titre -->
                <div>
                    <label for="title" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                        Titre de l'ouvrage <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        placeholder="Ex : Les Murmures du Fleuve Mono"
                        class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-agau-blue focus:ring-1 focus:ring-agau-blue transition-all @error('title') border-red-400 @enderror">
                </div>

                <!-- Catégorie / Genre -->
                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                        Genre / Catégorie littéraire <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" required
                        class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-agau-blue focus:ring-1 focus:ring-agau-blue transition-all @error('category') border-red-400 @enderror">
                        <option value="">Sélectionnez un genre</option>
                        @foreach(['Roman', 'Poésie', 'Nouvelles', 'Essai', 'Théâtre', 'Littérature Jeunesse', 'Conte & Légende', 'Biographie / Témoignage', 'Autre'] as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Note d'intention -->
            <div>
                <label for="intent_note" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                    Note d'intention de l'auteur <span class="text-red-500">*</span>
                </label>
                <textarea name="intent_note" id="intent_note" rows="3" required minlength="30" maxlength="3000"
                    placeholder="Pourquoi avez-vous écrit ce livre ? Quels thèmes explore-t-il et quel message souhaitez-vous porter ?"
                    class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl p-4 text-sm focus:bg-white focus:border-agau-blue focus:ring-1 focus:ring-agau-blue transition-all @error('intent_note') border-red-400 @enderror">{{ old('intent_note') }}</textarea>
                <p class="text-[11px] text-agau-muted mt-1">Éclairez le comité éditorial sur votre démarche artistique (minimum 30 caractères).</p>
            </div>

            <!-- Synopsis détaillé -->
            <div>
                <label for="synopsis" class="block text-xs font-bold uppercase tracking-wider text-agau-text mb-2">
                    Résumé & Synopsis complet de l'œuvre <span class="text-red-500">*</span>
                </label>
                <textarea name="synopsis" id="synopsis" rows="5" required minlength="50" maxlength="5000"
                    placeholder="Résumez l'intrigue, la structure, les protagonistes principaux et le dénouement de l'ouvrage..."
                    class="w-full bg-agau-border/10 border border-agau-border/40 rounded-xl p-4 text-sm focus:bg-white focus:border-agau-blue focus:ring-1 focus:ring-agau-blue transition-all @error('synopsis') border-red-400 @enderror">{{ old('synopsis') }}</textarea>
                <p class="text-[11px] text-agau-muted mt-1">Minimum 50 caractères. Le synopsis doit permettre d'apprécier la cohérence narrative.</p>
            </div>
        </div>

        <!-- Section 3 : Fichier & Engagements légaux -->
        <div class="bg-white border border-agau-border/50 rounded-2xl p-6 md:p-8 shadow-sm space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-agau-border/30">
                <div class="w-10 h-10 rounded-full bg-agau-blue/10 text-agau-blue flex items-center justify-center font-bold text-sm">
                    3
                </div>
                <div>
                    <h2 class="font-serif text-lg font-bold text-agau-text m-0">Manuscrit & Engagement Légal</h2>
                    <p class="text-xs text-agau-muted m-0">Déposez votre document d'œuvre intégral pour lecture.</p>
                </div>
            </div>

            <!-- Fichier du manuscrit -->
            <div class="border-2 border-dashed border-agau-border/80 hover:border-agau-blue/60 rounded-2xl p-6 md:p-8 text-center transition-all bg-agau-off-white/40">
                <div class="w-12 h-12 rounded-full bg-agau-blue/10 text-agau-blue flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-file-earmark-arrow-up text-2xl"></i>
                </div>
                <label for="manuscript_file" class="block text-sm font-bold text-agau-text cursor-pointer hover:text-agau-blue mb-1">
                    Sélectionner le manuscrit complet (.pdf ou .docx)
                </label>
                <p class="text-xs text-agau-muted mb-4">Taille maximale : 20 Mo. Les documents dactylographiés complets sont requis.</p>
                <input type="file" name="manuscript_file" id="manuscript_file" required accept=".pdf,.docx,.doc"
                    class="mx-auto block text-xs text-agau-muted file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-agau-blue file:text-white hover:file:bg-agau-blue-dark file:cursor-pointer transition-all">
                @error('manuscript_file')
                    <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Attestation sur l'honneur -->
            <div class="bg-agau-blue/5 border border-agau-blue/20 rounded-xl p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="certified_original" value="1" required {{ old('certified_original') ? 'checked' : '' }}
                        class="mt-1 w-4 h-4 rounded text-agau-blue focus:ring-agau-blue border-agau-border/60">
                    <div class="text-xs text-agau-text leading-relaxed">
                        <span class="font-bold">Attestation sur l'honneur d'originalité :</span>
                        Je certifie être l'unique auteur de cette œuvre, qu'elle ne constitue aucun plagiat et n'enfreint aucun droit de propriété intellectuelle tiers conformément aux lois en vigueur.
                    </div>
                </label>
                @error('certified_original')
                    <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Soumission -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
            <a href="{{ route('dashboard') }}" class="text-xs font-bold uppercase tracking-wider text-agau-muted hover:text-agau-text transition-colors">
                <i class="bi bi-arrow-left mr-1"></i> Revenir au tableau de bord
            </a>

            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-agau-blue text-white px-8 py-4 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-agau-blue-dark transition-all shadow-lg shadow-agau-blue/20 cursor-pointer">
                <i class="bi bi-send-fill"></i>
                <span>Transmettre ma candidature</span>
            </button>
        </div>
    </form>
</div>
@endsection
