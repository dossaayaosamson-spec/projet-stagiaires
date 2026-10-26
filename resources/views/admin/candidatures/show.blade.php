@extends('layouts.admin')

@section('title', "Candidature de {{ $candidature->nom }} {{ $candidature->prenoms }} — Admin AGAU")
@section('header-title', 'Dossier de candidature')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <a href="{{ route('admin.candidatures.index') }}" class="text-agau-muted text-sm hover:text-agau-blue flex items-center gap-1 mb-2 no-underline transition-colors">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
            <h1 class="font-lora text-3xl font-bold text-agau-text">{{ $candidature->nom }} {{ $candidature->prenoms }}</h1>
            <p class="text-sm text-agau-muted font-medium">Soumis le {{ $candidature->created_at->translatedFormat('d F Y \à H\hi') }}</p>
        </div>
        <a href="{{ route('admin.candidatures.download', $candidature) }}"
            class="inline-flex items-center gap-2 bg-agau-blue text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm no-underline">
            <i class="bi bi-cloud-download"></i> Télécharger le manuscrit
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3 text-sm text-green-700">
        <i class="bi bi-check-circle-fill text-green-500 text-base"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Infos personnelles --}}
            <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-agau-border/20 bg-agau-border/5">
                    <h2 class="font-bold text-agau-text text-sm uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-person-fill text-agau-blue"></i> Informations personnelles
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    @php
                        $info = [
                            ['label' => 'Nom complet', 'value' => "{$candidature->nom} {$candidature->prenoms}", 'icon' => 'person'],
                            ['label' => 'Genre', 'value' => $candidature->genre, 'icon' => 'gender-ambiguous'],
                            ['label' => 'Âge', 'value' => $candidature->age . ' ans', 'icon' => 'calendar3'],
                            ['label' => 'Nationalité', 'value' => $candidature->nationalite, 'icon' => 'flag'],
                            ['label' => 'Pays de résidence', 'value' => $candidature->pays_residence, 'icon' => 'globe-americas'],
                            ['label' => 'Ville', 'value' => "{$candidature->ville_residence}, {$candidature->quartier}", 'icon' => 'geo-alt'],
                            ['label' => 'WhatsApp', 'value' => $candidature->whatsapp, 'icon' => 'whatsapp'],
                            ['label' => 'Email', 'value' => $candidature->email, 'icon' => 'envelope'],
                            ['label' => 'Profession', 'value' => $candidature->profession, 'icon' => 'briefcase'],
                            ['label' => 'Compte AGAU', 'value' => $candidature->compte_agau ? 'Oui' : 'Non', 'icon' => 'person-circle'],
                        ];
                    @endphp
                    @foreach($info as $item)
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-0.5">
                            <i class="bi bi-{{ $item['icon'] }} mr-1"></i>{{ $item['label'] }}
                        </dt>
                        <dd class="text-agau-text font-medium">{{ $item['value'] }}</dd>
                    </div>
                    @endforeach
                    <div class="sm:col-span-2">
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-0.5">
                            <i class="bi bi-megaphone mr-1"></i>Source de découverte
                        </dt>
                        <dd class="text-agau-text font-medium">
                            {{ $candidature->source_decouverte }}
                            @if($candidature->source_decouverte_autre)
                                — <span class="italic text-agau-muted">{{ $candidature->source_decouverte_autre }}</span>
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

            {{-- Infos sur l'œuvre --}}
            <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-agau-border/20 bg-agau-border/5">
                    <h2 class="font-bold text-agau-text text-sm uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-book-fill text-agau-blue"></i> Informations sur l'œuvre
                    </h2>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                        <div>
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-0.5">Titre</dt>
                            <dd class="text-agau-text font-bold font-lora">{{ $candidature->titre_manuscrit }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-0.5">Genre littéraire</dt>
                            <dd><span class="bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">{{ $candidature->genre_litteraire }}</span></dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-0.5">Nombre de mots</dt>
                            <dd class="text-agau-text font-medium">{{ number_format($candidature->nombre_mots) }} mots</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-0.5">Déjà publié</dt>
                            <dd>
                                <span class="{{ $candidature->deja_publie ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }} px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                                    {{ $candidature->deja_publie ? 'Oui' : 'Non' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-0.5">Soumis ailleurs</dt>
                            <dd>
                                <span class="{{ $candidature->soumis_ailleurs ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }} px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight">
                                    {{ $candidature->soumis_ailleurs ? 'Oui' : 'Non' }}
                                </span>
                            </dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-2">Résumé</dt>
                        <dd class="text-agau-text font-lora leading-relaxed bg-agau-border/5 p-4 rounded-xl border border-agau-border/20 whitespace-pre-wrap">{{ $candidature->resume }}</dd>
                    </div>
                </div>
            </div>

            {{-- Engagements --}}
            <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-agau-border/20 bg-agau-border/5">
                    <h2 class="font-bold text-agau-text text-sm uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-check2-square text-agau-blue"></i> Engagements validés
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    @php
                        $engagements = [
                            'Déclaration d\'originalité' => $candidature->declaration_originalite,
                            'Acceptation du règlement'  => $candidature->acceptation_reglement,
                        ];
                    @endphp
                    @foreach($engagements as $label => $value)
                    <div class="flex items-center gap-2">
                        <i class="bi bi-{{ $value ? 'check-circle-fill text-green-500' : 'x-circle-fill text-red-400' }} text-base"></i>
                        <span class="text-agau-text font-medium">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Column: Status Management --}}
        <div class="space-y-5">
            {{-- Current Status --}}
            @php
                $statusConfig = [
                    'en_attente' => ['bg-amber-100 text-amber-800', 'bi-clock', 'En attente'],
                    'en_cours'   => ['bg-blue-100 text-blue-800', 'bi-arrow-clockwise', 'En cours d\'examen'],
                    'acceptee'   => ['bg-green-100 text-green-800', 'bi-check-circle', 'Acceptée'],
                    'refusee'    => ['bg-red-100 text-red-800', 'bi-x-circle', 'Refusée'],
                ];
                [$statusClass, $statusIcon, $statusLabel] = $statusConfig[$candidature->statut] ?? ['bg-gray-100 text-gray-700', 'bi-question', $candidature->statut];
            @endphp
            <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-3">Statut actuel</p>
                <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-bold {{ $statusClass }}">
                    <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
                </div>
            </div>

            {{-- Manage Status Form --}}
            <div class="bg-white border border-agau-border/40 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-agau-border/20 bg-agau-border/5">
                    <h2 class="font-bold text-agau-text text-sm uppercase tracking-widest">
                        <i class="bi bi-sliders mr-1 text-agau-blue"></i> Gérer le dossier
                    </h2>
                </div>
                <form method="POST" action="{{ route('admin.candidatures.update', $candidature) }}" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Changer le statut</label>
                        <select name="statut" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all bg-white">
                            <option value="en_attente" {{ $candidature->statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="en_cours"   {{ $candidature->statut === 'en_cours' ? 'selected' : '' }}>En cours d'examen</option>
                            <option value="acceptee"   {{ $candidature->statut === 'acceptee' ? 'selected' : '' }}>Acceptée</option>
                            <option value="refusee"    {{ $candidature->statut === 'refusee' ? 'selected' : '' }}>Refusée</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-1.5">Notes admin (internes)</label>
                        <textarea name="notes_admin" rows="5"
                            class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all resize-none"
                            placeholder="Vos observations sur ce dossier...">{{ old('notes_admin', $candidature->notes_admin) }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-agau-blue text-white py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm flex items-center justify-center gap-2">
                        <i class="bi bi-floppy-fill"></i> Enregistrer
                    </button>
                </form>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white border border-red-200 rounded-xl shadow-sm p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-red-400 mb-3">Zone dangereuse</p>
                <form method="POST" action="{{ route('admin.candidatures.destroy', $candidature) }}" onsubmit="return confirm('Supprimer définitivement ce dossier et le fichier manuscrit associé ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full flex items-center justify-center gap-2 border-2 border-red-300 text-red-600 py-2.5 px-4 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-red-50 transition-all">
                        <i class="bi bi-trash3-fill"></i> Supprimer ce dossier
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
