@extends('layouts.admin')

@section('title', 'Gestion du Carrousel — Admin AGAU Éditions')
@section('header-title', 'Carrousel')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Gestion du Carrousel</h1>
            <p class="text-sm text-agau-muted font-medium">Bannières, nouveautés et événements en page d'accueil.</p>
        </div>
        <a href="{{ route('admin.slides.create') }}" class="bg-agau-blue text-white px-6 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-images" style="font-size: 1.3rem"></i>
            Ajouter un Slide
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-800 px-6 py-4 rounded-xl text-xs font-bold flex items-start gap-3 mb-4">
            <span class="material-symbols-outlined text-green-500 text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-8 py-5">Visuel & Informations</th>
                    <th class="px-8 py-5 text-center">Ordre</th>
                    <th class="px-8 py-5 text-center">Statut</th>
                    <th class="px-8 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($slides as $slide)
                    <tr class="hover:bg-agau-blue/5 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-24 h-16 rounded-lg shadow-sm border border-agau-border/20 shrink-0 relative overflow-hidden bg-gray-100">
                                    <img src="{{ asset('storage/' . $slide->image_path) }}" class="absolute inset-0 w-full h-full object-cover">
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="font-bold text-agau-text truncate max-w-[250px]">{{ $slide->title ?: 'Sans titre' }}</span>
                                    <span class="text-[10px] text-agau-muted truncate max-w-[250px]">{{ $slide->link_url ?: 'Pas de lien' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center font-bold">
                            {{ $slide->order }}
                        </td>
                        <td class="px-8 py-5 text-center">
                            @if($slide->is_active)
                                <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Actif</span>
                            @else
                                <span class="bg-red-50 text-red-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Inactif</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex gap-3 justify-center">
                                <a href="{{ route('admin.slides.edit', $slide) }}" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Éditer">
                                    <i class="bi bi-pencil-square" style="font-size: 1.3rem"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.slides.destroy', $slide) }}" class="inline" onsubmit="return confirm('Supprimer ce slide définitivement ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 hover:bg-red-50 rounded-full text-agau-muted hover:text-red-600 transition-all border-none bg-transparent cursor-pointer">
                                        <i class="bi bi-trash" style="font-size: 1.3rem"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <span class="material-symbols-outlined text-4xl text-agau-muted/20">images</span>
                                <p class="text-agau-muted font-lora italic">Aucun slide dans le carrousel.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
