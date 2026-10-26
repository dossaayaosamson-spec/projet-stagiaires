@extends('layouts.admin')

@section('title', 'Modifier l\'Utilisateur — Admin AGAU Éditions')
@section('header-title', 'Gestion des Droits')@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
            <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Retour au Répertoire
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-8 border-b border-agau-border/20 bg-agau-border/5 flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-agau-blue text-white flex items-center justify-center font-bold text-2xl shrink-0 shadow-sm shadow-agau-blue/20 uppercase">
                {{ substr($user->prenom, 0, 1) }}{{ substr($user->nom_famille, 0, 1) }}
            </div>
            <div>
                <h2 class="font-lora text-2xl font-bold text-agau-text">{{ $user->nom_complet }}</h2>
                <p class="text-xs text-agau-muted font-medium mt-1">Modification des informations et des privilèges d'accès.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-8 space-y-8">
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
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Prénom *</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Nom de Famille *</label>
                    <input type="text" name="nom_famille" value="{{ old('nom_famille', $user->nom_famille) }}" required 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Adresse E-mail *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                    class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Alias (Nom d'affichage unique)</label>
                <input type="text" name="alias" value="{{ old('alias', $user->alias) }}" 
                    class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all"
                    placeholder="Laissez vide pour utiliser le nom réel">
            </div>

            <div class="space-y-4">
                <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Rôle & Privilèges Système</label>
                <div class="grid grid-cols-1 gap-3">
                    <label class="flex items-center gap-4 p-4 bg-white border border-agau-border/30 rounded-xl cursor-pointer hover:bg-agau-border/5 transition-all group relative overflow-hidden">
                        <input type="radio" name="role" value="client" {{ old('role', $user->role) === 'client' ? 'checked' : '' }} 
                            class="peer hidden">
                        <div class="w-5 h-5 border-2 border-agau-border/30 rounded-full peer-checked:border-agau-blue peer-checked:border-[6px] transition-all shrink-0"></div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-agau-text uppercase tracking-tight">Lecteur / Client</span>
                            <span class="text-[10px] text-agau-muted font-medium mt-0.5 whitespace-nowrap">Accès standard à la bibliothèque et aux achats.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 bg-white border border-agau-border/30 rounded-xl cursor-pointer hover:bg-agau-border/5 transition-all group relative overflow-hidden">
                        <input type="radio" name="role" value="auteur" {{ old('role', $user->role) === 'auteur' ? 'checked' : '' }} 
                            class="peer hidden">
                        <div class="w-5 h-5 border-2 border-agau-border/30 rounded-full peer-checked:border-agau-blue peer-checked:border-[6px] transition-all shrink-0"></div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-agau-text uppercase tracking-tight">Plume / Auteur</span>
                            <span class="text-[10px] text-agau-muted font-medium mt-0.5 whitespace-nowrap">Peut soumettre des manuscrits et gérer sa bibliographie.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 bg-white border border-agau-border/30 rounded-xl cursor-pointer hover:bg-agau-border/5 transition-all group relative overflow-hidden">
                        <input type="radio" name="role" value="admin" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }} 
                            class="peer hidden">
                        <div class="w-5 h-5 border-2 border-agau-border/30 rounded-full peer-checked:border-agau-blue peer-checked:border-[6px] transition-all shrink-0"></div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-agau-text uppercase tracking-tight">Architecte / Admin</span>
                            <span class="text-[10px] text-agau-muted font-medium mt-0.5 whitespace-nowrap">Contrôle total sur l'écosystème AGAU.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-agau-blue text-white py-4 rounded-xl text-xs font-bold tracking-[2px] uppercase cursor-pointer hover:bg-agau-blue-dark transition-all shadow-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Mettre à jour le Profil
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-8 py-4 bg-white border border-agau-border/40 text-agau-muted rounded-xl text-xs font-bold tracking-[2px] uppercase no-underline hover:bg-agau-border/5 transition-all">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
