@extends('layouts.admin')

@section('title', 'Modifier : ' . $event->titre . ' — Admin AGAU Éditions')
@section('header-title', 'Édition')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
            <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Retour aux Événements
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-8 border-b border-agau-border/20 bg-agau-border/5">
            <h2 class="font-lora text-2xl font-bold text-agau-text">Modifier l'Événement</h2>
            <p class="text-xs text-agau-muted font-medium mt-1">Mise à jour de <span class="text-agau-blue font-bold">"{{ $event->titre }}"</span>.</p>
        </div>

        <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data" class="p-8 space-y-8" id="event-form">
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
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Titre de l'événement *</label>
                    <input type="text" name="titre" value="{{ old('titre', $event->titre) }}" required 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Date & Heure *</label>
                    <input type="datetime-local" name="date_evenement" value="{{ old('date_evenement', $event->date_evenement->format('Y-m-d\TH:i')) }}" required 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Lien (Inscription/Détails)</label>
                    <input type="text" id="lien_inscription_raw" value="{{ old('lien_inscription', $event->lien_inscription) }}" placeholder="https://..." 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all">
                    <input type="hidden" name="lien_inscription" id="lien_inscription_encoded">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Petite Description</label>
                <textarea name="description" rows="3" 
                    class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 focus:border-agau-blue transition-all resize-none">{{ old('description', $event->description) }}</textarea>
            </div>

            <div class="p-6 bg-agau-blue/5 rounded-2xl border border-agau-blue/10 space-y-4">
                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-agau-blue uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">image</span>
                        Image d'illustration
                    </label>
                    
                    @if($event->image_path)
                        <div class="mb-4">
                            <img src="{{ $event->image_url }}" alt="Event illustration" class="w-full h-48 object-cover rounded-xl border border-agau-border/20 shadow-sm">
                        </div>
                    @endif

                    <input type="file" name="image_file" accept="image/*" 
                        class="text-xs text-agau-muted file:bg-white file:text-agau-blue file:border file:border-agau-blue/20 file:rounded-full file:px-4 file:py-1.5 file:mr-3 file:cursor-pointer file:font-bold hover:file:bg-agau-blue hover:file:text-white file:transition-all">
                    <p class="text-[9px] text-agau-muted font-medium italic">Laissez vide pour conserver l'image actuelle.</p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-agau-blue/10 flex-wrap gap-4">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $event->is_active) ? 'checked' : '' }} 
                                class="peer hidden">
                            <div class="w-5 h-5 border-2 border-agau-blue/30 rounded-md peer-checked:bg-agau-blue peer-checked:border-agau-blue transition-all flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-[14px] opacity-0 peer-checked:opacity-100 transition-opacity">check</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-agau-text group-hover:text-agau-blue transition-colors uppercase tracking-tight">Activer l'affichage</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_ecla" id="is_ecla" value="1" {{ old('is_ecla', $event->is_ecla) ? 'checked' : '' }} 
                                class="peer hidden">
                            <div class="w-5 h-5 border-2 border-indigo-300 rounded-md peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-[14px] opacity-0 peer-checked:opacity-100 transition-opacity">check</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-agau-text group-hover:text-indigo-600 transition-colors uppercase tracking-tight">Événement lié au Concours ECLA</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-agau-blue text-white py-4 rounded-xl text-xs font-bold tracking-[2px] uppercase cursor-pointer hover:bg-agau-blue-dark transition-all shadow-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Enregistrer les modifications
                </button>
                <a href="{{ route('admin.events.index') }}" class="px-8 py-4 bg-white border border-agau-border/40 text-agau-muted rounded-xl text-xs font-bold tracking-[2px] uppercase no-underline hover:bg-agau-border/5 transition-all">
                    Annuler
                </a>
            </div>
        </form>
        <script>
            document.getElementById('event-form').addEventListener('submit', function (e) {
                e.preventDefault();
                const raw = document.getElementById('lien_inscription_raw').value;
                if (raw) {
                    // btoa URL-safe : remplace les caractères problématiques
                    const encoded = btoa(raw)
                        .replace(/\+/g, '-')
                        .replace(/\//g, '_')
                        .replace(/=/g, '');
                    document.getElementById('lien_inscription_encoded').value = encoded;
                } else {
                    document.getElementById('lien_inscription_encoded').value = '';
                }
                this.submit();
            });
        </script>
    </div>
</div>
@endsection
