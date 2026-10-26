@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 md:px-8 py-8 md:py-12 max-w-7xl mx-auto">

    {{-- Navigation --}}
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('manuscripts.auteur') }}" class="inline-flex items-center gap-2 text-sm hover:underline transition-all duration-200 hover:text-[#0D3B64]" style="color:#1D6FB8;">
            <i class="bi bi-chevron-left"></i>
            Retour aux auteurs
        </a>
        <!-- <button onclick="deleteAuthor()" class="btn-danger px-4 py-2 text-sm rounded-xl flex items-center gap-2 transition-all duration-200 hover:scale-[1.02] hover:shadow-md">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
            </svg>
            Supprimer l'auteur
        </button> -->
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- COLONNE GAUCHE — Informations auteur + Manuscrits --}}
        <div class="lg:w-1/3 space-y-6">

            {{-- Carte auteur --}}
            <div class="bg-white rounded-2xl p-5 md:p-6 hover-lift" style="border:1px solid #E2E8F0;">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-blue-50 flex items-center justify-center shadow-sm" style="color:#1D6FB8;">
                        <i class="bi bi-person" style="font-size:1.75rem;"></i>
                    </div>
                    <div>
                        <h1 class="font-serif text-xl md:text-2xl font-semibold" style="color:#0D3B64;">{{ $author->name }}</h1>
                        <p class="text-xs" style="color:#94A3B8;">Auteur référencé</p>
                    </div>
                </div>
                <p class="text-sm leading-relaxed" style="color:#475569;">{{ $author->biography ?? 'Biographie non renseignée' }}</p>
                @if($author->typical_themes)
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($author->typical_themes as $theme)
                    <span class="text-[10px] px-2 py-1 rounded-full bg-gray-100" style="color:#1D6FB8;">#{{ $theme }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Style d'écriture --}}
            <div class="bg-white rounded-2xl p-5 md:p-6 hover-lift" style="border:1px solid #E2E8F0;">
                <h2 class="font-serif text-lg font-semibold mb-3 flex items-center gap-2" style="color:#0D3B64;"><i class="bi bi-pencil-square"></i> Style d'écriture</h2>
                <div id="styleDescription">
                    <p class="text-sm leading-relaxed" style="color:#475569;" id="styleText">
                        {{ $author->style_description ?? 'Aucune description. Ajoutez des manuscrits et cliquez sur "Entraîner l\'IA" pour analyser son style.' }}
                    </p>
                </div>
                <button onclick="trainAuthor()" id="trainBtn"
                    class="btn-warning mt-4 w-full px-4 py-2.5 rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-all duration-200 hover:scale-[1.02] hover:shadow-md">
                    <i class="bi bi-cpu"></i>
                    Entraîner l'IA sur ce style
                </button>
                <div id="trainResult" class="hidden mt-4"></div>
            </div>

            {{-- Manuscrits de référence --}}
            <div class="bg-white rounded-2xl p-5 md:p-6 hover-lift" style="border:1px solid #E2E8F0;">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-serif text-lg font-semibold flex items-center gap-2" style="color:#0D3B64;"><i class="bi bi-book"></i> Manuscrits de référence</h2>
                    <button onclick="openManuscriptModal()"
                        class="btn-primary text-xs px-3 py-1.5 rounded-lg flex items-center gap-1 transition-all duration-200 hover:scale-[1.02] hover:shadow-md">
                        <i class="bi bi-plus-lg"></i>
                        Ajouter
                    </button>
                </div>
                <p class="text-xs mb-3" style="color:#94A3B8;">
                    Ces manuscrits servent à entraîner l'IA sur le style de {{ $author->name }}.
                </p>
                <div class="space-y-2 max-h-80 overflow-y-auto" id="manuscriptList">
                    @forelse($manuscripts as $ms)
                    <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-gray-50 transition-colors" id="ms-{{ $ms->id }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate" style="color:#1A2A3A;">{{ $ms->title }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if($ms->is_processed ?? false)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-green-100 text-green-700 font-medium"><i class="bi bi-check-circle-fill"></i> Indexé</span>
                                @else
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-medium" id="ms-status-{{ $ms->id }}"><i class="bi bi-hourglass-split"></i> Non indexé</span>
                                    <button onclick="indexManuscript({{ $ms->id }})"
                                        class="text-[10px] px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors font-medium cursor-pointer">
                                        Indexer
                                    </button>
                                @endif
                            </div>
                        </div>
                        <button onclick="deleteManuscript({{ $ms->id }})"
                            class="text-gray-400 hover:text-red-500 ml-2 flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full hover:bg-red-50 transition-all text-lg leading-none cursor-pointer">
                            &times;
                        </button>
                    </div>
                    @empty
                    <div class="text-center py-6" id="emptyManuscripts">
                        <i class="bi bi-file-earmark-text text-gray-300" style="font-size:2.5rem;"></i>
                        <p class="text-sm mt-2" style="color:#94A3B8;">Aucun manuscrit ajouté</p>
                        <p class="text-xs mt-1" style="color:#CBD5E1;">Ajoutez des textes pour entraîner l'IA</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════
             COLONNE DROITE — Assistant RAG + Relecture
        ════════════════════════════════════════════════════════════════ --}}
        <div class="lg:w-2/3 space-y-6">

           
{{-- ── BLOC 1 : Réécriture stylistique IA (gratuit/payant) ── --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden hover-lift" style="border:1px solid #E2E8F0;">
    <div class="px-5 md:px-6 py-4" style="background:linear-gradient(135deg,#0D3B64 0%,#1D6FB8 100%);">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-pencil text-white" style="font-size:1.25rem;"></i>
            </div>
            <div>
                <h2 class="text-white font-serif text-base md:text-lg font-semibold">Réécriture stylistique IA</h2>
                <p class="text-white/70 text-xs">Réécrit votre texte dans le style de {{ $author->name }}</p>
            </div>
        </div>
    </div>

    <div class="flex border-b overflow-x-auto" style="border-color:#E2E8F0;">
        <button onclick="switchRewriteTab('rewrite_paste')" id="rewriteTabPasteBtn"
            class="px-5 md:px-6 py-3 text-sm font-medium transition-all whitespace-nowrap cursor-pointer"
            style="background:#EEF5FD; color:#1D6FB8; border-bottom:2px solid #1D6FB8;">
            <i class="bi bi-pencil"></i> Coller le texte
        </button>
        <button onclick="switchRewriteTab('rewrite_upload')" id="rewriteTabUploadBtn"
            class="px-5 md:px-6 py-3 text-sm font-medium transition-all whitespace-nowrap cursor-pointer"
            style="background:#F8FAFC; color:#94A3B8;">
            <i class="bi bi-folder"></i> Upload fichier
        </button>
    </div>

    <div class="p-4 md:p-6">
        {{-- Coller --}}
        <div id="rewrite_paste" class="rewrite-tab-content">
            <textarea id="rewriteText" rows="6"
                class="w-full p-3 md:p-4 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all resize-y"
                style="border-color:#E2E8F0;"
                placeholder="Collez le texte à réécrire dans le style de {{ $author->name }}..."></textarea>
            <p id="rewriteWordCount" class="text-xs mt-1 text-right" style="color:#94A3B8;">0 mot</p>
        </div>

        {{-- Upload --}}
        <div id="rewrite_upload" class="rewrite-tab-content hidden">
            <div id="rewriteDropZone"
                class="rounded-xl p-6 text-center cursor-pointer transition-all bg-gray-50 hover:bg-gray-100 hover:border-[#1D6FB8]"
                style="border:2px dashed #CBD5E1;">
                <input type="file" id="rewriteFileInput" accept=".pdf,.docx,.txt,.doc" class="hidden">
                <i class="bi bi-cloud-upload text-gray-400" style="font-size:2.5rem;"></i>
                <p class="text-xs text-gray-500 mb-1 mt-2">Glissez un fichier ou cliquez</p>
                <p class="text-[10px] text-gray-400">TXT, PDF, DOCX — max 20 Mo</p>
            </div>
            <div id="rewriteFileInfo" class="mt-3 hidden">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-50" style="border:1px solid #BDDCF5;">
                    <i class="bi bi-file-earmark flex-shrink-0" style="color:#1D6FB8; font-size:1rem;"></i>
                    <span id="rewriteFileName" class="flex-1 text-sm font-medium truncate" style="color:#1D6FB8;"></span>
                    <button onclick="clearRewriteFile()" class="text-gray-400 hover:text-red-500 text-lg cursor-pointer">&times;</button>
                </div>
            </div>
        </div>

        {{-- Style cible (optionnel) --}}
        <div class="mt-4">
            <label class="block text-sm font-semibold mb-2 flex items-center gap-2" style="color:#1A2A3A;"><i class="bi bi-palette"></i> Style cible (optionnel)</label>
            <input type="text" id="customStyleRewrite"
                class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all"
                style="border-color:#E2E8F0;"
                placeholder="Laissez vide pour utiliser le style de {{ $author->name }}">
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Gratuit --}}
            <div class="rounded-xl p-4 transition-all hover:shadow-md" style="background:#F0FDF4; border:1px solid #BBF7D0;">
                <div class="flex items-center gap-2 mb-2">
                    <i class="bi bi-gift text-lg" style="color:#15803D;"></i>
                    <div>
                        <p class="font-semibold text-sm" style="color:#14532D;">Aperçu Gratuit</p>
                        <p class="text-[10px]" style="color:#16A34A;">300 premiers mots · Instantané</p>
                    </div>
                </div>
                <ul class="text-xs space-y-1 mb-3" style="color:#15803D;">
                    <li>✓ Réécriture de l’extrait</li>
                    <li>✓ Affichage immédiat</li>
                </ul>
                <button onclick="previewRewrite()" id="previewRewriteBtn"
                    class="btn-free w-full py-2 rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
                    style="background:#22C55E; color:#fff;">
                    <i class="bi bi-eye"></i> Voir l'aperçu gratuit
                </button>
            </div>

            {{-- Payant --}}
            <div class="rounded-xl p-4 relative overflow-hidden transition-all hover:shadow-md" style="background:linear-gradient(135deg,#0D3B64,#1D6FB8);">
                <div class="absolute top-2 right-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-yellow-400 text-yellow-900">⭐ COMPLET</div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="bi bi-file-text text-lg text-white"></i>
                    <div>
                        <p class="font-semibold text-sm text-white">Réécriture Complète</p>
                    </div>
                </div>
                <ul class="text-xs space-y-1 mb-3 text-white/80">
                    <li>✓ Texte intégral réécrit</li>
                    <li>✓ Téléchargement PDF / Word</li>
                </ul>
                <div class="flex gap-3 mb-3">
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="rewriteFormat" value="pdf" checked class="accent-yellow-400">
                        <span class="text-xs text-white"><i class="bi bi-file-pdf"></i> PDF</span>
                    </label>
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="rewriteFormat" value="docx" class="accent-yellow-400">
                        <span class="text-xs text-white"><i class="bi bi-file-word"></i> Word</span>
                    </label>
                </div>
                <input type="email" id="rewriteCustomerEmail"
                    placeholder="Email (optionnel)"
                    class="w-full px-3 py-1.5 rounded-lg text-xs border-0 focus:outline-none mb-2"
                    style="background:rgba(255,255,255,0.15); color:white;">
                <button onclick="initiateRewritePayment()" id="payRewriteBtn"
                    class="btn-pay w-full py-2 rounded-lg text-xs font-bold transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
                    style="background:#EAB308; color:#1a0a00;">
                    <i class="bi bi-credit-card"></i> Payer 5000 FCFA &amp; Télécharger
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Résultat de l'aperçu gratuit (réécriture) --}}
<div id="rewritePreviewCard" class="hidden bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #E2E8F0;">
    <div class="bg-green-50 px-5 py-3 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <i class="bi bi-check-circle" style="color:#22C55E; font-size:1rem;"></i>
            <h3 class="font-semibold text-sm" style="color:#0D3B64;">Aperçu Gratuit — Texte réécrit</h3>
        </div>
        <span id="previewWordCountBadge" class="text-xs" style="color:#94A3B8;"></span>
    </div>
    <div id="rewritePreviewContent" class="p-5 prose prose-sm max-w-none" style="color:#1E293B;"></div>
    <div class="px-5 py-4 bg-blue-50" style="border-top:1px solid #E2E8F0;">
        <p class="text-sm font-medium mb-2 flex items-center gap-2" style="color:#0D3B64;"><i class="bi bi-rocket"></i> Obtenez la réécriture complète pour 5000 FCFA</p>
        <button onclick="document.getElementById('payRewriteBtn').scrollIntoView({behavior:'smooth'})"
            class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] hover:shadow-md">
            Payer & Télécharger
        </button>
    </div>
</div>

{{-- Résultat après paiement (réécriture complète) --}}
<div id="rewritePaymentResultCard" class="hidden bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #E2E8F0;">
    <div class="px-5 py-3" style="background:linear-gradient(135deg,#0D3B64,#1D6FB8);">
        <div class="flex items-center gap-2">
            <i class="bi bi-check-circle text-white" style="font-size:1rem;"></i>
            <h3 class="font-semibold text-sm text-white">Paiement confirmé – Réécriture en cours</h3>
        </div>
    </div>
    <div id="rewritePaymentResultContent" class="p-5">
        <div class="text-center py-8">
            <div class="loading-spinner mx-auto mb-4" style="border-top-color:#EAB308;"></div>
            <p class="text-sm" style="color:#64748B;">L'IA réécrit l'intégralité de votre texte…</p>
        </div>
    </div>
</div>

            {{-- ── BLOC 2 : Relecture Gratuite / Payante ── --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover-lift" style="border:1px solid #E2E8F0;">
                <div class="px-5 md:px-6 py-4" style="background:linear-gradient(135deg,#1A0A00 0%,#78350F 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-file-text text-white" style="font-size:1.25rem;"></i>
                        </div>
                        <div>
                            <h2 class="text-white font-serif text-base md:text-lg font-semibold">Relecture & Correction de manuscrit</h2>
                            <p class="text-white/70 text-xs">Aperçu gratuit · Relecture complète 500 FCFA</p>
                        </div>
                    </div>
                </div>
                <div class="flex border-b overflow-x-auto" style="border-color:#E2E8F0;">
                    <button onclick="switchReviewTab('rl_paste')" id="rlTabPasteBtn"
                        class="px-5 md:px-6 py-3 text-sm font-medium transition-all whitespace-nowrap cursor-pointer"
                        style="background:#FEF9C3; color:#92400E; border-bottom:2px solid #EAB308;">
                        <i class="bi bi-pencil"></i> Coller le texte
                    </button>
                    <button onclick="switchReviewTab('rl_upload')" id="rlTabUploadBtn"
                        class="px-5 md:px-6 py-3 text-sm font-medium transition-all whitespace-nowrap cursor-pointer"
                        style="background:#F8FAFC; color:#94A3B8;">
                        <i class="bi bi-folder"></i> Upload fichier
                    </button>
                </div>
                <div class="p-4 md:p-6">
                    {{-- Coller --}}
                    <div id="rl_paste" class="rl-tab-content">
                        <textarea id="reviewText" rows="6"
                            class="w-full p-3 md:p-4 rounded-xl border focus:outline-none transition-all resize-y"
                            style="border-color:#E2E8F0; focus:border-color:#EAB308;"
                            placeholder="Collez votre roman, nouvelle, essai ou tout autre texte à relire…"></textarea>
                        <p id="rlWordCount" class="text-xs mt-1 text-right" style="color:#94A3B8;">0 mot</p>
                    </div>
                    {{-- Upload --}}
                    <div id="rl_upload" class="rl-tab-content hidden">
                        <div id="rlDropZone"
                            class="rounded-xl p-6 text-center cursor-pointer transition-all bg-gray-50 hover:bg-gray-100 hover:border-[#EAB308]"
                            style="border:2px dashed #CBD5E1;">
                            <input type="file" id="rlFileInput" accept=".pdf,.docx,.txt,.doc" class="hidden">
                            <i class="bi bi-cloud-upload text-gray-400" style="font-size:2.5rem;"></i>
                            <p class="text-xs text-gray-500 mb-1 mt-2">Glissez un fichier ou cliquez</p>
                            <p class="text-[10px] text-gray-400">TXT, PDF, DOCX — max 20 Mo</p>
                        </div>
                        <div id="rlFileInfo" class="mt-3 hidden">
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-yellow-50" style="border:1px solid #FDE68A;">
                                <i class="bi bi-file-earmark flex-shrink-0" style="color:#92400E; font-size:1rem;"></i>
                                <span id="rlFileName" class="flex-1 text-sm font-medium truncate" style="color:#92400E;"></span>
                                <button onclick="clearRlFile()" class="text-gray-400 hover:text-red-500 text-lg cursor-pointer">&times;</button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Gratuit --}}
                        <div class="rounded-xl p-4 transition-all hover:shadow-md" style="background:#F0FDF4; border:1px solid #BBF7D0;">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="bi bi-gift text-lg" style="color:#15803D;"></i>
                                <div>
                                    <p class="font-semibold text-sm" style="color:#14532D;">Aperçu Gratuit</p>
                                    <p class="text-[10px]" style="color:#16A34A;">300 premiers mots · Instantané</p>
                                </div>
                            </div>
                            <ul class="text-xs space-y-1 mb-3" style="color:#15803D;">
                                <li>✓ Correction de l'extrait</li>
                                <li>✓ Liste des problèmes détectés</li>
                            </ul>
                                <button onclick="launchFreeReview()" id="freeReviewBtn"
                                    class="btn-free w-full py-2 rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
                                    style="background:#22C55E; color:#fff;">
                                    <i class="bi bi-eye"></i> Voir l'aperçu gratuit
                                </button>
                        </div>
                        {{-- Payant --}}
                        <div class="rounded-xl p-4 relative overflow-hidden transition-all hover:shadow-md" style="background:linear-gradient(135deg,#1A0A00,#78350F);">
                            <div class="absolute top-2 right-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-yellow-400 text-yellow-900">⭐ COMPLET</div>
                            <div class="flex items-center gap-2 mb-2">
                                <i class="bi bi-book text-lg text-white"></i>
                                <div>
                                    <p class="font-semibold text-sm text-white">Relecture Complète</p>
                                </div>
                            </div>
                            <ul class="text-xs space-y-1 mb-3 text-white/80">
                                <li>✓ Tout le document corrigé</li>
                                <li>✓ Téléchargement PDF / Word</li>
                            </ul>
                            <div class="flex gap-3 mb-3">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="rlFormat" value="pdf" checked class="accent-yellow-400">
                                    <span class="text-xs text-white"><i class="bi bi-file-pdf"></i> PDF</span>
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="rlFormat" value="docx" class="accent-yellow-400">
                                    <span class="text-xs text-white"><i class="bi bi-file-word"></i> Word</span>
                                </label>
                            </div>
                            <input type="email" id="rlCustomerEmail"
                                placeholder="Email (optionnel)"
                                class="w-full px-3 py-1.5 rounded-lg text-xs border-0 focus:outline-none mb-2"
                                style="background:rgba(255,255,255,0.15); color:white;">
                            <button onclick="launchPayment()" id="payReviewBtn"
                                class="btn-pay w-full py-2 rounded-lg text-xs font-bold transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
                                style="background:#EAB308; color:#1a0a00;">
                                <i class="bi bi-credit-card"></i> Payer 5000 FCFA &amp; Télécharger
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Résultat aperçu gratuit --}}
            <div id="freeResultCard" class="hidden bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #E2E8F0;">
                <div class="bg-green-50 px-5 py-3 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check-circle" style="color:#22C55E; font-size:1rem;"></i>
                        <h3 class="font-semibold text-sm" style="color:#0D3B64;">Aperçu Gratuit — Résultats</h3>
                    </div>
                    <span id="freeWordCountBadge" class="text-xs" style="color:#94A3B8;"></span>
                </div>
                <div id="freeResultContent" class="p-5"></div>
                <div class="px-5 py-4 bg-blue-50" style="border-top:1px solid #E2E8F0;">
                    <p class="text-sm font-medium mb-2 flex items-center gap-2" style="color:#0D3B64;"><i class="bi bi-rocket"></i> Obtenez la relecture complète pour 100 FCFA</p>
                    <button onclick="document.getElementById('payReviewBtn').scrollIntoView({behavior:'smooth'})"
                        class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] hover:shadow-md">
                        Payer & Télécharger
                    </button>
                </div>
            </div>

            {{-- Résultat après paiement --}}
            <div id="paymentResultCard" class="hidden bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #E2E8F0;">
                <div class="px-5 py-3" style="background:linear-gradient(135deg,#1A0A00,#78350F);">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check-circle text-white" style="font-size:1rem;"></i>
                        <h3 class="font-semibold text-sm text-white">Paiement confirmé</h3>
                    </div>
                </div>
                <div id="paymentResultContent" class="p-5">
                    <div class="text-center py-8">
                        <div class="loading-spinner mx-auto mb-4" style="border-top-color:#EAB308;"></div>
                        <p class="text-sm" style="color:#64748B;">L'IA effectue la relecture complète…</p>
                    </div>
                </div>
            </div>

        </div>{{-- fin colonne droite --}}
    </div>
</div>

{{-- MODAL — Ajouter un manuscrit de référence (pour l'entraînement IA) --}}
<div id="manuscriptModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background:rgba(0,0,0,0.5);">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full p-5 md:p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-serif text-xl font-semibold" style="color:#0D3B64;">Ajouter un manuscrit de référence</h3>
                    <p class="text-xs mt-0.5" style="color:#94A3B8;">Ce texte sera utilisé pour entraîner l'IA sur le style de {{ $author->name }}</p>
                </div>
                <button onclick="closeManuscriptModal()" class="p-1 text-gray-400 hover:text-gray-600 text-2xl leading-none cursor-pointer">&times;</button>
            </div>
            <div class="flex border-b mb-4" style="border-color:#E2E8F0;">
                <button onclick="switchManuscriptTab('ms_upload')" id="msTabUploadBtn"
                    class="px-4 py-2 text-sm font-medium transition-all cursor-pointer"
                    style="background:#EEF5FD; color:#1D6FB8; border-bottom:2px solid #1D6FB8;">
                    <i class="bi bi-folder"></i> Upload fichier
                </button>
                <button onclick="switchManuscriptTab('ms_paste')" id="msTabPasteBtn"
                    class="px-4 py-2 text-sm font-medium transition-all cursor-pointer"
                    style="background:#F8FAFC; color:#94A3B8;">
                    <i class="bi bi-pencil"></i> Coller le texte
                </button>
            </div>
            <form id="manuscriptForm" onsubmit="addManuscript(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:#1A2A3A;">Titre du manuscrit *</label>
                        <input type="text" name="title" required
                            class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all"
                            style="border-color:#E2E8F0;">
                    </div>
                    <div id="msTabUpload" class="manuscript-tab-content">
                        <label class="block text-sm font-semibold mb-1.5" style="color:#1A2A3A;">Fichier du manuscrit *</label>
                        <div id="msDropZone"
                            class="rounded-xl p-6 text-center cursor-pointer transition-all bg-gray-50 hover:bg-gray-100 hover:border-[#1D6FB8]"
                            style="border:2px dashed #CBD5E1;">
                            <input type="file" id="msFileInput" accept=".pdf,.docx,.txt,.doc" class="hidden">
                            <i class="bi bi-cloud-upload text-gray-400" style="font-size:2.5rem;"></i>
                            <p class="text-sm text-gray-500">Glissez votre fichier ou <span class="text-[#1D6FB8] font-medium cursor-pointer" onclick="document.getElementById('msFileInput').click()">parcourez</span></p>
                            <p class="text-[10px] mt-1 text-gray-400">PDF, DOCX, TXT — max 20 Mo</p>
                        </div>
                        <div id="msFileInfo" class="mt-2 hidden">
                            <div class="flex items-center gap-2 p-2.5 rounded-xl bg-blue-50" style="border:1px solid #BDDCF5;">
                                <i class="bi bi-file-earmark flex-shrink-0" style="color:#1D6FB8; font-size:1rem;"></i>
                                <span id="msFileName" class="flex-1 text-xs truncate font-medium" style="color:#1D6FB8;"></span>
                                <button onclick="clearManuscriptFile()" type="button" class="text-gray-400 hover:text-red-500 text-lg leading-none cursor-pointer">&times;</button>
                            </div>
                        </div>
                        <textarea name="content" id="msUploadedText" class="hidden"></textarea>
                    </div>
                    <div id="msTabPaste" class="manuscript-tab-content hidden">
                        <label class="block text-sm font-semibold mb-1.5" style="color:#1A2A3A;">Contenu du manuscrit *</label>
                        <textarea name="content_paste" id="msPasteText" rows="8"
                            class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all resize-y"
                            style="border-color:#E2E8F0;"
                            placeholder="Collez ici le texte complet du manuscrit de référence…"></textarea>
                        <p class="text-[10px] mt-1" style="color:#94A3B8;">Plus le texte est long et représentatif, meilleur sera l'entraînement.</p>
                    </div>
                </div>
                <div class="mt-4 p-3 rounded-xl flex items-start gap-2" style="background:#EEF5FD; border:1px solid #BDDCF5;">
                    <i class="bi bi-info-circle flex-shrink-0 mt-0.5" style="color:#1D6FB8;"></i>
                    <p class="text-xs" style="color:#1D6FB8;">
                        Après ajout, le manuscrit sera automatiquement <strong>indexé</strong> puis vous pourrez
                        <strong>entraîner l'IA</strong> pour analyser le style de {{ $author->name }}.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 mt-5">
                    <button type="button" onclick="closeManuscriptModal()"
                        class="flex-1 px-4 py-2.5 rounded-xl border hover:bg-gray-50 transition-all text-sm cursor-pointer"
                        style="border-color:#E2E8F0;">Annuler</button>
                    <button type="submit" id="msSubmitBtn"
                        class="btn-primary flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-[1.02] hover:shadow-md">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL paiement --}}
<div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background:rgba(0,0,0,0.6);">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-serif text-xl font-semibold" style="color:#0D3B64;">Paiement sécurisé</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 text-2xl cursor-pointer">&times;</button>
            </div>
            <div id="modalContent">
                <div class="text-center py-6">
                    <div class="loading-spinner mx-auto mb-4"></div>
                    <p class="text-sm" style="color:#64748B;">Création de la transaction FedaPay…</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.loading-spinner {
    width: 36px; height: 36px;
    border: 3px solid #EEF2F7;
    border-top: 3px solid #1D6FB8;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
.hover-lift { transition: box-shadow .2s, transform .2s; }
.hover-lift:hover { transform: translateY(-1px); box-shadow: 0 4px 20px rgba(0,0,0,.07); }
#rlCustomerEmail::placeholder { color: rgba(255,255,255,0.45); }

/* Classes boutons réutilisables */
.btn-primary {
    background: #1D6FB8;
    color: #fff;
    cursor: pointer;
}
.btn-primary:hover {
    background: #0D3B64;
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.btn-danger {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    cursor: pointer;
}
.btn-danger:hover {
    background: #fecaca;
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.btn-warning {
    background: #EAB308;
    color: #fff;
    cursor: pointer;
}
.btn-warning:hover {
    background: #ca8a04;
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.btn-free {
    background: #22C55E;
    color: #fff;
    cursor: pointer;
}
.btn-free:hover {
    background: #16a34a;
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.btn-pay {
    background: #EAB308;
    color: #1a0a00;
    cursor: pointer;
}
.btn-pay:hover {
    background: #ca8a04;
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
</style>

@push('scripts')
<script>
    window.authorId    = {{ $author->id }};
    window.authorName  = @json($author->name);
    window.deleteAuthorRoute = '{{ route("manuscripts.deleteauteur", $author->id) }}';
    window.authorsListRoute  = '{{ route("manuscripts.auteur") }}';
    window.csrfToken   = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/author-detail.js') }}?v=2"></script>
@endpush
@endsection