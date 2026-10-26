@extends('layouts.admin')

@section('title', 'Gestion des Événements — Admin AGAU Éditions')
@section('header-title', 'Actualités & Événements')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Événements</h1>
            <p class="text-sm text-agau-muted font-medium">Gérez les dédicaces, salons et actualités de la maison.</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="bg-agau-blue text-white px-6 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase no-underline hover:bg-agau-blue-dark transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-plus-circle-dotted" style="font-size: 1.2rem;"></i>
            Ajouter un événement
        </a>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-8 py-5">Événement</th>
                    <th class="px-8 py-5">Date</th>
                    <th class="px-8 py-5">Statut</th>
                    <th class="px-8 py-5 text-center">Gestion</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($events as $event)
                    <tr class="hover:bg-agau-blue/5 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded shadow-sm overflow-hidden relative border border-agau-border/20 bg-agau-border/10">
                                    @if($event->image_path)
                                        <img src="{{ $event->image_url }}" class="absolute inset-0 w-full h-full object-cover">
                                    @else
                                        <div class="flex items-center justify-center h-full text-agau-muted/30">
                                            <span class="material-symbols-outlined text-xs">image</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-agau-text group-hover:text-agau-blue transition-colors">{{ $event->titre }}</span>
                                    <span class="text-[11px] text-agau-muted font-medium truncate max-w-xs">{{ Str::limit($event->description, 50) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5 font-lora font-bold text-agau-blue-dark">
                            {{ $event->date_evenement->translatedFormat('d/m/Y à H:i') }}
                        </td>
                        <td class="px-8 py-5">
                            @if($event->is_active)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">Actif</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">Inactif</span>
                            @endif
                            
                            @if($event->date_evenement->isPast())
                                <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight ml-2">Passé</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex gap-3 justify-center">
                                <a href="{{ route('admin.events.edit', $event) }}" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Éditer">
                                    <i class="bi bi-pencil-square" style="font-size: 1.3rem;"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline" onsubmit="return confirm('Supprimer cet événement ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 hover:bg-red-50 rounded-full text-agau-muted hover:text-red-600 transition-all border-none bg-transparent cursor-pointer">
                                        <i class="bi bi-trash" style="font-size: 1.3rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <span class="material-symbols-outlined text-4xl text-agau-muted/20">event_busy</span>
                                <p class="text-agau-muted font-lora italic">Aucun événement programmé.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($events->hasPages())
        <div class="mt-8">
            {{ $events->links() }}
        </div>
    @endif
</div>
@endsection
