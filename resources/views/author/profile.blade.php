@extends('layouts.dashboard')

@section('title', 'Mon Profil Auteur — AGAU Éditions')

@section('content')
<div class="max-w-[900px] mx-auto">
    <!-- Header -->
    <div class="mb-[32px]">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('author.dashboard') }}" class="text-agau-blue text-[0.7rem] font-bold uppercase tracking-wider no-underline hover:underline flex items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                Retour au dashboard
            </a>
        </div>
        <h1 class="font-serif text-[2.2rem] text-agau-text font-bold m-0">Mon Profil Auteur</h1>
        <p class="text-[0.9rem] text-agau-muted mt-2 font-medium leading-relaxed">Personnalisez votre identité publique pour attirer plus de lecteurs.</p>
    </div>

    @if(session('success'))
    <div class="mb-[32px] p-[16px] bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-[0.85rem] font-semibold flex items-center gap-3 shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
            <i class="bi bi-check2"></i>
        </div>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white border border-agau-border/40 rounded-2xl overflow-hidden shadow-sm">
        <form action="{{ route('author.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-12">
            @csrf
            @method('PUT')

            <!-- Avatar & Profile Intro -->
            <div class="flex flex-col md:flex-row items-center md:items-start gap-10 mb-12 pb-12 border-b border-agau-border/10">
                <div class="relative group">
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-3xl overflow-hidden border-4 border-white shadow-xl bg-agau-off-white flex items-center justify-center group-hover:scale-[1.02] transition-transform duration-300">
                        @if($author->avatar)
                            <img src="{{ asset('storage/' . $author->avatar) }}" alt="{{ $author->nom }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-agau-blue/20 to-agau-blue/5 flex items-center justify-center">
                                <span class="text-4xl font-black text-agau-blue/30">{{ strtoupper(substr($author->nom, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <label for="avatar" class="absolute -bottom-2 -right-2 w-10 h-10 bg-agau-blue text-white rounded-xl flex items-center justify-center cursor-pointer shadow-lg hover:bg-[#1B5E8A] hover:scale-110 transition-all z-10 border-2 border-white">
                        <i class="bi bi-camera-fill text-lg"></i>
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*">
                    </label>
                </div>
                
                <div class="flex-1 text-center md:text-left pt-2">
                    <h3 class="text-xl font-bold text-agau-text m-0 mb-2">Photo de profil</h3>
                    <p class="text-sm text-agau-muted m-0 mb-6 max-w-sm">Une photo professionnelle aide à établir une connexion de confiance avec vos lecteurs.</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-2">
                        <span class="px-3 py-1 bg-agau-off-white text-agau-muted text-[10px] font-bold uppercase rounded-full">JPG, PNG, WebP</span>
                        <span class="px-3 py-1 bg-agau-off-white text-agau-muted text-[10px] font-bold uppercase rounded-full">Max 2 Mo</span>
                    </div>
                    @error('avatar')
                        <p class="text-red-500 text-xs font-bold mt-4 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 gap-8">
                <div class="space-y-2">
                    <label for="nom" class="text-xs font-black text-agau-text uppercase tracking-widest flex items-center gap-2">
                        Nom d'auteur / Pseudonyme
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-agau-muted group-focus-within:text-agau-blue transition-colors"></i>
                        <input type="text" id="nom" name="nom" value="{{ old('nom', $author->nom) }}" 
                            class="w-full bg-agau-off-white/50 border border-agau-border/40 rounded-xl py-4 pl-12 pr-4 text-[0.95rem] text-agau-text focus:outline-none focus:border-agau-blue focus:bg-white transition-all shadow-inner @error('nom') border-red-500 @enderror"
                            placeholder="Votre nom de plume">
                    </div>
                    @error('nom')
                        <p class="text-red-500 text-xs font-bold mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="bio" class="text-xs font-black text-agau-text uppercase tracking-widest">Votre Biographie</label>
                    <textarea id="bio" name="bio" rows="8" 
                        class="w-full bg-agau-off-white/50 border border-agau-border/40 rounded-xl py-4 px-5 text-[0.95rem] text-agau-text focus:outline-none focus:border-agau-blue focus:bg-white transition-all shadow-inner leading-relaxed @error('bio') border-red-500 @enderror"
                        placeholder="Partagez votre univers, vos inspirations et votre parcours littéraire avec vos lecteurs...">{{ old('bio', $author->bio) }}</textarea>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-[0.7rem] text-agau-muted font-medium italic">Ce texte apparaîtra sur votre page auteur publique.</p>
                        @error('bio')
                            <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-12 pt-8 border-t border-agau-border/10 flex flex-col sm:flex-row justify-end gap-4">
                <button type="reset" class="px-8 py-4 text-sm font-bold text-agau-muted hover:text-agau-text transition-colors uppercase tracking-widest">
                    Réinitialiser
                </button>
                <button type="submit" class="bg-agau-blue text-white border-none py-4 px-10 text-sm font-black tracking-widest uppercase cursor-pointer rounded-xl transition-all shadow-lg shadow-agau-blue/20 hover:bg-[#1B5E8A] hover:translate-y-[-2px] active:translate-y-0">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-agau-off-white { background-color: #F8FAFC; }
    .border-agau-border { border-color: #E2E8F0; }
    .text-agau-text { color: #1E293B; }
    .text-agau-muted { color: #64748B; }
    .text-agau-blue { color: #1B6CA8; }
    .bg-agau-blue { background-color: #1B6CA8; }
</style>
@endsection
