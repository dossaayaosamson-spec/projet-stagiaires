@extends('layouts.admin')

@section('title', 'Gestion de la Communauté — Admin AGAU Éditions')
@section('header-title', 'Membres & Collaborateurs')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <div>
            <h1 class="font-lora text-3xl font-bold text-agau-text">Communauté</h1>
            <p class="text-sm text-agau-muted font-medium">Répertoire des membres et collaborateurs AGAU.ED.</p>
        </div>
        <div class="bg-agau-blue/5 border border-agau-blue/10 px-4 py-2 rounded-lg">
            <span class="text-[10px] font-bold text-agau-blue uppercase tracking-widest">{{ $users->total() }} âmes inscrites</span>
        </div>
    </div>

    <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-agau-border/5 text-agau-text/50 text-[10px] font-bold uppercase tracking-[2px] border-b border-agau-border/30">
                    <th class="px-8 py-5">Identité</th>
                    <th class="px-8 py-5">Privilèges</th>
                    <th class="px-8 py-5">Communication</th>
                    <th class="px-8 py-5">Arrivée</th>
                    <th class="px-8 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-agau-text divide-y divide-agau-border/20">
                @forelse($users as $user)
                    <tr class="hover:bg-agau-blue/5 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-agau-blue/10 flex items-center justify-center text-agau-blue font-bold text-[12px] shadow-sm transform group-hover:scale-110 transition-transform">
                                    {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom_famille, 0, 1)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-agau-text group-hover:text-agau-blue transition-colors">{{ $user->nom_complet }}</span>
                                    <span class="text-[9px] text-agau-muted font-bold tracking-tighter uppercase opacity-60">ID #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            @if($user->role === 'admin')
                                <span class="bg-agau-blue-dark text-white px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-tight shadow-sm flex items-center gap-1 w-fit">
                                    <i class="bi bi-shield-lock" style="font-size: 1.2rem;"></i>
                                    Admin
                                </span>
                            @elseif($user->role === 'auteur')
                                <span class="bg-agau-blue text-white px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-tight shadow-sm flex items-center gap-1 w-fit">
                                    <i class="bi bi-pencil-square" style="font-size: 1.2rem;"></i>
                                    Plume
                                </span>
                            @else
                                <span class="bg-agau-border/10 text-agau-muted px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-tight flex items-center gap-1 w-fit">
                                    <i class="bi bi-person" style="font-size: 1.2rem;"></i>
                                    Lecteur
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-agau-text">{{ $user->email }}</span>
                                <span class="text-[10px] text-agau-muted italic">{{ $user->telephone ?? 'Non renseigné' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs text-agau-muted font-medium italic">{{ $user->created_at->translatedFormat('M Y') }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex gap-3 justify-center items-center">
                                @if($user->id !== auth()->id())
                                    <a href="{{ route('admin.users.edit', $user) }}" class="p-2 hover:bg-agau-blue/5 rounded-full text-agau-muted hover:text-agau-blue transition-all" title="Modifier">
                                        <i class="bi bi-pencil-square" style="font-size: 1.2rem;"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Suspendre définitivement ce membre ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 hover:bg-red-50 rounded-full text-agau-muted hover:text-red-600 transition-all border-none bg-transparent cursor-pointer">
                                            <i class="bi bi-ban" style="font-size: 1.2rem;"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[9px] font-bold text-agau-blue uppercase tracking-widest bg-agau-blue/5 px-3 py-1 rounded-full">Profil Actuel</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <span class="material-symbols-outlined text-4xl text-agau-muted/20">group_off</span>
                                <p class="text-agau-muted font-lora italic">Le répertoire est curieusement vide...</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
        <div class="mt-8">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
