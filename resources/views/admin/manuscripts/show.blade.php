@extends('layouts.admin')

@section('title', 'Détail manuscrit — Admin AGAU Éditions')
@section('header-title', 'Détail du manuscrit')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.manuscripts.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-agau-muted hover:text-agau-blue transition-colors uppercase tracking-widest no-underline group">
            <i class="bi bi-arrow-left text-[18px] group-hover:-translate-x-1 transition-transform"></i>
            Retour à la liste
        </a>

        <a href="{{ route('admin.manuscripts.download', $manuscript) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-agau-border/40 rounded-xl text-[10px] font-bold uppercase tracking-widest text-agau-muted hover:bg-agau-border/5 transition-all no-underline">
            <i class="bi bi-download text-[18px]"></i>
            Télécharger
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1.6fr_0.9fr] gap-8">
        <div class="space-y-8">
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-8 border-b border-agau-border/20 bg-agau-border/5 flex justify-between items-start">
                    <div>
                        <h2 class="font-lora text-2xl font-bold text-agau-text">{{ $manuscript->title ?? 'Manuscrit sans titre' }}</h2>
                        <p class="text-xs text-agau-muted font-medium mt-1">Soumis le {{ $manuscript->submitted_at ? $manuscript->submitted_at->translatedFormat('d F Y à H:i') : '-' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-bold text-agau-muted uppercase tracking-widest block mb-2">Statut actuel</span>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'analyzed' => 'bg-blue-50 text-blue-700 border-blue-100',
                                'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'rejected' => 'bg-red-50 text-red-700 border-red-100',
                            ];
                            $labelMap = [
                                'pending' => 'En attente',
                                'analyzed' => 'En cours d\'analyse',
                                'accepted' => 'Accepté',
                                'rejected' => 'Refusé',
                            ];
                            $currentClass = $statusClasses[$manuscript->status] ?? 'bg-gray-50 text-gray-700 border-gray-100';
                            $currentLabel = $labelMap[$manuscript->status] ?? ucfirst($manuscript->status ?? 'Inconnu');
                        @endphp
                        <span class="{{ $currentClass }} px-4 py-1.5 border rounded-full text-[10px] font-bold uppercase tracking-widest shadow-sm">
                            {{ $currentLabel }}
                        </span>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] font-bold text-agau-muted uppercase tracking-widest mb-2">Auteur</p>
                            <p class="text-sm font-bold text-agau-text">{{ $manuscript->author_name ?? ($manuscript->user?->nom_complet ?? 'Auteur inconnu') }}</p>
                            <p class="text-xs text-agau-muted mt-1">{{ $manuscript->user?->email ?? 'Email non renseigné' }}</p>
                        </div>

                        <div>
                            <p class="text-[10px] font-bold text-agau-muted uppercase tracking-widest mb-2">Catégorie</p>
                            <p class="text-sm font-bold text-agau-text">{{ $manuscript->category ?? 'Non précisée' }}</p>
                        </div>
                    </section>

                    <section>
                        <p class="text-[10px] font-bold text-agau-muted uppercase tracking-widest mb-3">Synopsis</p>
                        <div class="p-4 bg-agau-border/5 rounded-xl border border-agau-border/10 text-sm text-agau-text leading-relaxed">
                            {{ $manuscript->synopsis ?: 'Aucun synopsis fourni.' }}
                        </div>
                    </section>

                    @if($manuscript->submission_source === 'candidature_auteur' || $manuscript->pen_name)
                    <section class="p-5 bg-amber-50/70 border border-amber-200/70 rounded-xl space-y-4">
                        <div class="flex items-center gap-2 text-amber-800 font-bold text-xs uppercase tracking-wider">
                            <i class="bi bi-star-fill text-amber-600"></i> Dossier de Candidature Auteur
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-agau-muted font-medium">Nom de plume / Alias :</span>
                                <p class="font-bold text-agau-text text-sm">{{ $manuscript->pen_name ?: 'Non renseigné' }}</p>
                            </div>
                            <div>
                                <span class="text-agau-muted font-medium">Résidence :</span>
                                <p class="font-bold text-agau-text">{{ $manuscript->city ? $manuscript->city . ', ' : '' }}{{ $manuscript->country ?: 'Non renseigné' }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-agau-muted font-medium">Biographie :</span>
                                <p class="text-agau-text mt-1 bg-white p-3 rounded-lg border border-amber-100 leading-relaxed">{{ $manuscript->bio ?: 'Non renseignée' }}</p>
                            </div>
                            @if($manuscript->intent_note)
                            <div class="sm:col-span-2">
                                <span class="text-agau-muted font-medium">Note d'intention :</span>
                                <p class="text-agau-text mt-1 bg-white p-3 rounded-lg border border-amber-100 leading-relaxed">{{ $manuscript->intent_note }}</p>
                            </div>
                            @endif
                        </div>
                    </section>
                    @endif

                    @if($manuscript->contract)
                    <section class="p-5 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-emerald-800 font-bold text-xs uppercase tracking-wider flex items-center gap-2">
                                <i class="bi bi-check-circle-fill text-emerald-600"></i> Contrat Associé
                            </span>
                            <span class="bg-emerald-200 text-emerald-900 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
                                {{ $manuscript->contract->statut }}
                            </span>
                        </div>
                        <p class="text-xs text-emerald-900">
                            Type : <strong>{{ $manuscript->contract->type_contrat === 'compte_editeur' ? 'Compte d\'Éditeur' : 'Distribution Numérique' }}</strong> 
                            | Taux de redevance : <strong>{{ $manuscript->contract->taux_redevance }}%</strong>
                        </p>
                    </section>
                    @endif

                    <section>
                        <p class="text-[10px] font-bold text-agau-muted uppercase tracking-widest mb-3">Décision & Notes de l’admin</p>
                        <form method="POST" action="{{ route('admin.manuscripts.update', $manuscript) }}" id="manuscriptStatusForm">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label for="status" class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-2">Changer le statut</label>
                                    <select id="status" name="status" class="w-full px-3 py-2.5 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all bg-white" onchange="toggleContractFields(this.value)">
                                        <option value="pending" {{ $manuscript->status === 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="analyzed" {{ $manuscript->status === 'analyzed' ? 'selected' : '' }}>En cours d'analyse</option>
                                        <option value="accepted" {{ $manuscript->status === 'accepted' ? 'selected' : '' }}>Accepté (Promouvoir en auteur)</option>
                                        <option value="rejected" {{ $manuscript->status === 'rejected' ? 'selected' : '' }}>Refusé</option>
                                    </select>
                                </div>

                                <div id="contractFields" class="{{ $manuscript->status === 'accepted' || !$manuscript->contract ? '' : 'hidden' }} p-4 bg-agau-blue/5 border border-agau-blue/20 rounded-xl space-y-3">
                                    <p class="text-xs font-bold text-agau-blue uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="bi bi-file-earmark-text"></i> Paramètres du Contrat (en cas d'acceptation)
                                    </p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold uppercase text-agau-muted mb-1">Type de Contrat</label>
                                            <select name="type_contrat" id="type_contrat" class="w-full px-3 py-2 border border-agau-border/50 rounded-lg text-xs bg-white" onchange="updateDefaultRate(this.value)">
                                                <option value="compte_editeur" {{ old('type_contrat', $manuscript->contract?->type_contrat) === 'compte_editeur' ? 'selected' : '' }}>Compte d'éditeur (Classique)</option>
                                                <option value="distribution_numerique" {{ old('type_contrat', $manuscript->contract?->type_contrat) === 'distribution_numerique' ? 'selected' : '' }}>Distribution Numérique / Coédition</option>
                                                <option value="autre" {{ old('type_contrat', $manuscript->contract?->type_contrat) === 'autre' ? 'selected' : '' }}>Autre convention</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold uppercase text-agau-muted mb-1">Taux Redevance (%)</label>
                                            <input type="number" step="0.5" min="0" max="100" name="taux_redevance" id="taux_redevance" value="{{ old('taux_redevance', $manuscript->contract?->taux_redevance ?? 10.00) }}" class="w-full px-3 py-2 border border-agau-border/50 rounded-lg text-xs bg-white" placeholder="ex: 10.00">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="admin_notes" class="block text-[10px] font-bold uppercase tracking-widest text-agau-muted mb-2">Commentaire interne ou motif</label>
                                    <textarea id="admin_notes" name="admin_notes" rows="5" class="w-full px-3 py-3 border border-agau-border/50 rounded-lg text-sm focus:outline-none focus:border-agau-blue transition-all" placeholder="Ajouter des notes éditoriales ou explications…">{{ old('admin_notes', $manuscript->admin_notes) }}</textarea>
                                </div>

                                <button type="submit" class="bg-agau-blue text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase hover:bg-agau-blue-dark transition-all shadow-sm cursor-pointer">
                                    <i class="bi bi-check2-circle"></i> Enregistrer la décision
                                </button>
                            </div>
                        </form>

                        <script>
                            function toggleContractFields(status) {
                                const box = document.getElementById('contractFields');
                                if (status === 'accepted') {
                                    box.classList.remove('hidden');
                                } else {
                                    box.classList.add('hidden');
                                }
                            }
                            function updateDefaultRate(type) {
                                const rateInput = document.getElementById('taux_redevance');
                                if (type === 'compte_editeur') {
                                    rateInput.value = '10.00';
                                } else if (type === 'distribution_numerique') {
                                    rateInput.value = '50.00';
                                }
                            }
                        </script>
                    </section>
                </div>
            </div>
        </div>

        <aside class="space-y-8">
            <div class="bg-white border border-agau-border/40 rounded-2xl shadow-sm p-6">
                <h3 class="text-[10px] font-bold text-agau-muted uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="bi bi-clock-history text-[18px]"></i>
                    Journal d’administration
                </h3>

                @forelse($logs as $log)
                    <div class="space-y-2 border-b border-agau-border/10 pb-3 mb-3 last:border-0 last:pb-0 last:mb-0">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-bold text-agau-text">{{ $log->action }}</span>
                            <span class="text-[10px] text-agau-muted">{{ $log->created_at ? $log->created_at->translatedFormat('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div class="text-[11px] text-agau-muted">
                            Par {{ $log->admin?->prenom ?? 'Admin' }} {{ $log->admin?->nom_famille ?? '' }}
                        </div>
                        @if(!empty($log->details))
                            <div class="text-[11px] text-agau-text bg-agau-border/5 rounded-lg p-3">
                                @foreach($log->details as $key => $value)
                                    <div class="flex gap-2">
                                        <span class="text-agau-muted font-medium">{{ str_replace('_', ' ', $key) }}:</span>
                                        <span>{{ is_array($value) ? json_encode($value) : $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-4 bg-agau-border/5 rounded-xl text-sm text-agau-muted">
                        Aucun journal d’administration pour ce manuscrit.
                    </div>
                @endforelse
            </div>
        </aside>
    </div>
</div>
@endsection
