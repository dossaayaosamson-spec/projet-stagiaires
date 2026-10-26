@extends('layouts.admin')

@section('title', 'Modifier le Slide — Admin AGAU Éditions')
@section('header-title', 'Carrousel')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.slides.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
            <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Retour au Carrousel
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-8 border-b border-agau-border/20 bg-agau-border/5">
            <h2 class="font-lora text-2xl font-bold text-agau-text">Modifier un Slide</h2>
        </div>

        <form method="POST" action="{{ route('admin.slides.update', $slide) }}" enctype="multipart/form-data" class="p-8 space-y-8">
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
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Image du Slide</label>
                    @if($slide->image_path)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $slide->image_path) }}" alt="Aperçu" class="w-48 rounded-lg border border-agau-border/30">
                        </div>
                    @endif
                    <input type="file" name="image_file" accept="image/*" 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-agau-blue file:text-white hover:file:bg-agau-blue-dark">
                    <p class="text-[9px] text-agau-muted font-medium italic mt-1">Laissez vide pour conserver l'image actuelle.</p>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Titre (Optionnel)</label>
                    <input type="text" name="title" value="{{ old('title', $slide->title) }}" 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Lien de redirection (Optionnel)</label>
                    <input type="url" name="link_url" value="{{ old('link_url', $slide->link_url) }}" placeholder="https://..." 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 transition-all">
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Description (Optionnel)</label>
                    <textarea name="description" rows="3"
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 transition-all resize-none">{{ old('description', $slide->description) }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-agau-muted uppercase tracking-widest ml-1">Ordre d'affichage</label>
                    <input type="number" name="order" value="{{ old('order', $slide->order) }}" min="0" 
                        class="w-full bg-agau-border/5 border border-agau-border/30 rounded-xl px-4 py-3 text-sm text-agau-text focus:outline-none focus:ring-1 focus:ring-agau-blue/30 transition-all">
                </div>

                <div class="space-y-2 flex items-center mt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $slide->is_active) ? 'checked' : '' }} class="w-5 h-5 text-agau-blue rounded border-agau-border/30">
                        <span class="text-sm font-bold text-agau-text">Activer ce slide</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-agau-blue text-white py-4 rounded-xl text-xs font-bold tracking-[2px] uppercase cursor-pointer hover:bg-agau-blue-dark transition-all">
                    Mettre à jour le slide
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
