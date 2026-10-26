@extends('layouts.app')

@section('content')

{{-- ─────────────── HERO BANNER ─────────────── --}}
<section style="background: linear-gradient(135deg, #0D3B64 0%, #1D6FB8 100%);" class="py-14 md:py-20 px-[6%] relative overflow-hidden">
  <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full opacity-10" style="background:#5BC4F5;"></div>
  <div class="absolute bottom-0 left-[45%] w-56 h-56 rounded-full opacity-5" style="background:#fff;"></div>

  <div class="relative z-10 flex flex-col md:flex-row md:items-end md:justify-between gap-6 max-w-5xl">
    <div>
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-5"
           style="background:rgba(91,196,245,0.15); border:1px solid rgba(91,196,245,0.35);">
        <span class="w-[5px] h-[5px] rounded-full" style="background:#7EC8F0;"></span>
        <span class="text-[0.62rem] font-bold tracking-[2.5px] uppercase" style="color:#7EC8F0;">Résultat d'analyse</span>
      </div>
      <h1 class="font-serif text-white text-[2rem] md:text-[2.6rem] leading-[1.25] font-semibold mb-2" id="manuscript-title">{{ $manuscript->title ?? 'Manuscrit analysé' }}</h1>
      <p class="text-sm font-medium" style="color:rgba(255,255,255,0.65);">
        Auteur : <span style="color:#7EC8F0;" id="author-name">{{ $manuscript->author_name ?? 'Auteur' }}</span>
      </p>
    </div>
    <div class="flex flex-col items-start md:items-end gap-3">
      <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium"
           style="background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.7); border:1px solid rgba(255,255,255,0.15);">
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3.5l2 1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
        Analysé le <span id="analysis-date">{{ $manuscript->analyzed_at ? $manuscript->analyzed_at->format('d/m/Y à H:i') : date('d/m/Y à H:i') }}</span>
      </div>
      <a href="{{ route('manuscripts.create') }}" class="btn-secondary no-print inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold tracking-[1.5px] uppercase transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
         style="background:rgba(255,255,255,0.12); color:#fff; border:1px solid rgba(255,255,255,0.25);">
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Nouvelle analyse
      </a>
    </div>
  </div>
</section>

{{-- ─────────────── MAIN CONTENT (RENDU DYNAMIQUE) ─────────────── --}}
<div class="px-[5%] md:px-[8%] py-[50px] md:py-[70px] max-w-6xl mx-auto space-y-6">
    <div id="analysis-container">
        <div class="text-center py-12">
            <div class="inline-block w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            <p class="mt-4 text-gray-500">Chargement de l'analyse...</p>
        </div>
    </div>

    @auth
    <div class="mt-8 text-center border-t border-gray-200 pt-8 no-print">
        <a href="{{ auth()->user()->role === 'author' ? route('author.dashboard') : route('client.dashboard') }}" class="btn-secondary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] hover:shadow-md" style="background:#fff; color:#0D3B64; border:1px solid #E2E8F0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            Retourner au Dashboard
        </a>
    </div>
    @endauth
</div>

@push('scripts')
<script>
// ─────────────────────────────────────────────────────────────
// Configuration
// ─────────────────────────────────────────────────────────────
const MANUSCRIPT_ID   = "{{ $manuscript->id }}";
const ACCESS_TOKEN    = "{{ $access_token ?? '' }}";
const DJANGO_API_URL  = "https://agaueditions-ia.saeicubetech.com";
const PAYMENT_INIT_URL= "{{ route('payment.initiate') }}";
const CSRF_TOKEN      = "{{ csrf_token() }}";

// ─────────────────────────────────────────────────────────────
// Chargement des données depuis Django
// ─────────────────────────────────────────────────────────────
async function loadAnalysis() {
    const container = document.getElementById('analysis-container');
    try {
        let url = `${DJANGO_API_URL}/api/analysis/${MANUSCRIPT_ID}/`;
        if (ACCESS_TOKEN) url += `?token=${ACCESS_TOKEN}`;
        
        console.log('📡 Appel API:', url);  // ← Vérifiez l'URL dans la console
        
        const resp = await fetch(url);
        
        // Si la réponse n'est pas OK, on affiche le texte brut
        if (!resp.ok) {
            const text = await resp.text();
            console.error('❌ Réponse non JSON (status ' + resp.status + '):', text.substring(0, 500));
            throw new Error(`Erreur HTTP ${resp.status} : ${text.substring(0, 100)}`);
        }
        
        const data = await resp.json();
        if (!data.success) throw new Error(data.error || 'Erreur chargement');

        // Mise à jour du bandeau
        if (data.title) document.getElementById('manuscript-title').innerText = data.title;
        if (data.author_name) document.getElementById('author-name').innerHTML = data.author_name;
        if (data.created_at) document.getElementById('analysis-date').innerHTML = new Date(data.created_at).toLocaleString();

        renderAnalysis(data);
    } catch (err) {
        container.innerHTML = `<div class="bg-red-50 p-6 rounded-xl text-red-800">
            <strong>Erreur de chargement</strong><br>
            ${err.message}<br>
            <small>Vérifiez que l’API Django est accessible et que l’URL est correcte.</small>
        </div>`;
        console.error('Erreur loadAnalysis:', err);
    }
} 
// ─────────────────────────────────────────────────────────────
// Rendu principal
// ─────────────────────────────────────────────────────────────
function renderAnalysis(data) {
    const container = document.getElementById('analysis-container');
    if (!data.full_access) {
        // ---------- APERÇU GRATUIT ----------
        container.innerHTML = buildPreviewHTML(data);
    } else {
        // ---------- ANALYSE COMPLÈTE ----------
        container.innerHTML = buildFullHTML(data);
        // Ajouter la bannière de téléchargement (identique à l'original)
        if (ACCESS_TOKEN) {
            const downloadDiv = document.createElement('div');
            downloadDiv.className = 'no-print mt-8 pt-6';
            downloadDiv.style.borderTop = '1px solid #E2E8F0';
            downloadDiv.innerHTML = `
                <div class="rounded-2xl p-6 md:p-8" style="background:linear-gradient(135deg,#EEF6FF 0%,#F0F7FF 100%); border:1px solid #BDDCF5;">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0" style="background:#1D6FB8;">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 16V4M8 12l4 4 4-4" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 18v1a2 2 0 002 2h12a2 2 0 002-2v-1" stroke="white" stroke-width="1.6" stroke-linecap="round"/></svg>
                            </div>
                            <div><h4 class="font-serif text-lg font-semibold" style="color:#0D3B64;">Télécharger le rapport complet</h4><p class="text-sm" style="color:#64748B;">Version PDF ou Word avec toutes les corrections détaillées</p></div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <a href="${DJANGO_API_URL}/api/download/${MANUSCRIPT_ID}/?token=${ACCESS_TOKEN}&format=pdf" class="btn-primary-download inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all duration-200 hover:scale-[1.02] hover:shadow-md" style="background:#1D6FB8; color:#fff;">📄 PDF — 1000 FCFA</a>
                            <a href="${DJANGO_API_URL}/api/download/${MANUSCRIPT_ID}/?token=${ACCESS_TOKEN}&format=docx" class="btn-secondary-download inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all duration-200 hover:scale-[1.02] hover:shadow-md" style="background:#fff; color:#1D6FB8; border:1.5px solid #1D6FB8;">📝 Word — 1000 FCFA</a>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(downloadDiv);
        }
    }
}

// ─────────────────────────────────────────────────────────────
// Construction de l'aperçu (version gratuite)
// ─────────────────────────────────────────────────────────────
function buildPreviewHTML(data) {
    const score = data.overall_score || 75;
    const badgeBg = score >= 80 ? '#EAF3DE' : (score >= 60 ? '#FAEEDA' : '#FCEBEB');
    const badgeColor = score >= 80 ? '#3B6D11' : (score >= 60 ? '#854F0B' : '#A32D2D');
    const badgeLabel = score >= 80 ? 'Excellent' : (score >= 60 ? 'Bien' : 'À améliorer');
    const dashOffset = 289 - (289 * (score / 100));

    const strengthsHtml = (data.strengths_preview || []).map(s => {
        let text = typeof s === 'object' ? (s.description || s.category || JSON.stringify(s)) : s;
        return `<li class="flex items-start gap-3 text-sm pb-3" style="color:#334155; border-bottom:1px solid #F1F5F9;">
                    <span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#EAF3DE; color:#639922;">✓</span>
                    ${escapeHtml(text)}
                </li>`;
    }).join('') || '<li class="text-sm text-gray-500">Aucun point fort détecté.</li>';

    const weaknessesHtml = (data.weaknesses_preview || []).map(w => {
        let text = typeof w === 'object' ? (w.issue || w.description || JSON.stringify(w)) : w;
        return `<li class="flex items-start gap-3 text-sm pb-3" style="color:#334155; border-bottom:1px solid #F1F5F9;">
                    <span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#FCEBEB; color:#E24B4A;">✗</span>
                    ${escapeHtml(text)}
                </li>`;
    }).join('') || '<li class="text-sm text-gray-500">Aucun point d\'amélioration détecté.</li>';

    return `
        <div class="grid grid-cols-1 lg:grid-cols-[200px_1fr] gap-6">
            <div class="bg-white rounded-2xl p-7 flex flex-col items-center text-center hover-lift" style="border:1px solid #E2E8F0;">
                <p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-4" style="color:#1D6FB8;">Score global</p>
                <div class="relative mb-3">
                    <svg width="110" height="110" viewBox="0 0 110 110">
                        <circle cx="55" cy="55" r="46" stroke="#EEF2F7" stroke-width="7" fill="none"/>
                        <circle cx="55" cy="55" r="46" stroke="#1D6FB8" stroke-width="7" fill="none" stroke-dasharray="289" stroke-dashoffset="${dashOffset}" stroke-linecap="round" transform="rotate(-90 55 55)"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="font-serif text-[2.2rem] font-bold leading-none" style="color:#0D3B64;">${score}</span>
                        <span class="text-xs" style="color:#94A3B8;">/100</span>
                    </div>
                </div>
                <span class="inline-block text-xs font-bold px-4 py-1.5 rounded-full" style="background:${badgeBg}; color:${badgeColor};">${badgeLabel}</span>
                <button onclick="initiatePayment()" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl transition">Débloquer l'analyse complète (1000 FCFA)</button>
            </div>
            <div class="space-y-4">
                <div class="bg-white rounded-2xl p-6 md:p-7 flex-1 hover-lift" style="border:1px solid #E2E8F0;">
                    <p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-3" style="color:#1D6FB8;">Résumé (aperçu)</p>
                    <p class="text-sm leading-relaxed" style="color:#334155;">${escapeHtml(data.summary_preview || 'Aucun résumé disponible.')}</p>
                </div>
                <div class="bg-white rounded-2xl p-6 md:p-7 flex-1 hover-lift" style="border:1px solid #E2E8F0;">
                    <p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-3" style="color:#1D6FB8;">Conclusion (aperçu)</p>
                    <p class="text-sm leading-relaxed" style="color:#334155;">${escapeHtml(data.conclusion_preview || 'Aucune conclusion disponible.')}</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl p-7 hover-lift" style="border:1px solid #E2E8F0;">
                <div class="flex items-center gap-2.5 mb-5"><div class="w-2.5 h-2.5 rounded-full" style="background:#639922;"></div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase" style="color:#639922;">Points forts (extrait)</p></div>
                <h3 class="font-serif text-lg font-semibold mb-1" style="color:#0D3B64;">Ce qui fonctionne</h3>
                <div class="accent-bar mb-5" style="background:#639922;"></div>
                <ul class="space-y-3">${strengthsHtml}</ul>
            </div>
            <div class="bg-white rounded-2xl p-7 hover-lift" style="border:1px solid #E2E8F0;">
                <div class="flex items-center gap-2.5 mb-5"><div class="w-2.5 h-2.5 rounded-full" style="background:#E24B4A;"></div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase" style="color:#E24B4A;">Points à améliorer (extrait)</p></div>
                <h3 class="font-serif text-lg font-semibold mb-1" style="color:#0D3B64;">À retravailler</h3>
                <div class="accent-bar mb-5" style="background:#E24B4A;"></div>
                <ul class="space-y-4">${weaknessesHtml}</ul>
            </div>
        </div>
        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-200 text-center mt-6">
            <p class="text-sm text-amber-800">🔒 <strong>Analyse complète verrouillée</strong><br>Corrections détaillées, réécritures,Vérification de cohérence narrative, analyse stylistique avancée et recommandations personnalisées disponibles après paiement.</p>
            <button onclick="initiatePayment()" class="mt-4 bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-6 rounded-xl transition-all duration-200 cursor-pointer hover:shadow-lg hover:-translate-y-0.5">Payer et débloquer</button>
        </div>
    `;
}

// ─────────────────────────────────────────────────────────────
// Construction de la page complète (identique à l'original)
// ─────────────────────────────────────────────────────────────
function buildFullHTML(data) {
    const score = data.overall_score || 75;
    const badgeBg = score >= 80 ? '#EAF3DE' : (score >= 60 ? '#FAEEDA' : '#FCEBEB');
    const badgeColor = score >= 80 ? '#3B6D11' : (score >= 60 ? '#854F0B' : '#A32D2D');
    const badgeLabel = score >= 80 ? 'Excellent' : (score >= 60 ? 'Bien' : 'À améliorer');
    const dashOffset = 289 - (289 * (score / 100));

    const strengthsList = (data.strengths || []).map(s => {
        if (typeof s === 'object') {
            return `<li class="flex flex-col gap-2 text-sm pb-3" style="color:#334155; border-bottom:1px solid #F1F5F9;">
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#EAF3DE; color:#639922;">✓</span>
                            <span><strong>${escapeHtml(s.category || 'Point fort')}</strong> : ${escapeHtml(s.description || s)}</span>
                        </div>
                        ${s.location ? `<p class="text-xs ml-8" style="color:#94A3B8;">📍 ${escapeHtml(s.location)}</p>` : ''}
                        ${s.reason ? `<p class="text-xs ml-8" style="color:#639922;">💡 ${escapeHtml(s.reason)}</p>` : ''}
                    </li>`;
        }
        return `<li class="flex items-start gap-3 text-sm pb-3" style="color:#334155; border-bottom:1px solid #F1F5F9;">
                    <span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#EAF3DE; color:#639922;">✓</span>
                    ${escapeHtml(s)}
                </li>`;
    }).join('');

    const weaknessesList = (data.weaknesses || []).map(w => {
        if (typeof w === 'object') {
            return `<li class="pb-4" style="border-bottom:1px solid #F1F5F9;">
                        <div class="flex items-start gap-3 mb-3">
                            <span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#FCEBEB; color:#E24B4A;">✗</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold mb-1" style="color:#E24B4A;">${escapeHtml(w.issue || 'Problème identifié')}</p>
                                <p class="text-xs" style="color:#94A3B8;">📍 ${escapeHtml(w.location || 'Emplacement non spécifié')}</p>
                                ${w.priority ? `<span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded mt-1" style="background:${w.priority === 'haute' ? '#FCEBEB' : '#F1F5F9'}; color:${w.priority === 'haute' ? '#E24B4A' : '#64748B'};">${escapeHtml(w.priority.toUpperCase())} priorité</span>` : ''}
                            </div>
                        </div>
                        ${(w.original_text || w.suggestion) ? `
                        <div class="ml-8 space-y-3 mt-2">
                            ${w.original_text ? `<div class="p-3 rounded-lg" style="background:#FEF2F2; border-left:3px solid #E24B4A;"><p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color:#E24B4A;">❌ Avant</p><p class="text-sm italic" style="color:#475569;">« ${escapeHtml(w.original_text)} »</p></div>` : ''}
                            ${w.suggestion ? `<div class="p-3 rounded-lg" style="background:#F0FDF4; border-left:3px solid #22C55E;"><p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color:#22C55E;">✅ Après (suggestion)</p><p class="text-sm" style="color:#475569;">${escapeHtml(w.suggestion)}</p></div>` : ''}
                        </div>` : ''}
                    </li>`;
        }
        return `<li class="flex items-start gap-3 text-sm pb-3" style="color:#334155; border-bottom:1px solid #F1F5F9;">
                    <span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#FCEBEB; color:#E24B4A;">✗</span>${escapeHtml(w)}
                </li>`;
    }).join('');

    const corrections = (data.detailed_corrections || []).map(c => `
        <div class="border rounded-xl overflow-hidden" style="border-color:#E2E8F0;">
            <div class="flex flex-wrap items-center gap-2 px-4 py-2" style="background:#F8FAFC;">
                <span class="text-xs font-bold px-2 py-1 rounded-full" style="background:${c.type === 'grammaire' ? '#FCEBEB' : (c.type === 'style' ? '#FFFBF3' : '#EAF3DE')}; color:${c.type === 'grammaire' ? '#E24B4A' : (c.type === 'style' ? '#BA7517' : '#3B6D11')};">${escapeHtml(c.type || 'amélioration')}</span>
                <span class="text-xs" style="color:#94A3B8;">📍 ${escapeHtml(c.location || 'Emplacement non spécifié')}</span>
            </div>
            <div class="p-4 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="p-3 rounded-lg" style="background:#FEF2F2;"><p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color:#E24B4A;">❌ Avant</p><p class="text-sm italic" style="color:#475569;">« ${escapeHtml(c.original_text || 'Non spécifié')} »</p></div>
                    <div class="p-3 rounded-lg" style="background:#F0FDF4;"><p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color:#22C55E;">✅ Après (proposition)</p><p class="text-sm" style="color:#475569;">${escapeHtml(c.corrected_text || c.suggestion || 'Non spécifié')}</p></div>
                </div>
                ${c.explanation ? `<p class="text-xs mt-2 pt-2" style="color:#64748B; border-top:1px solid #E2E8F0;">💡 ${escapeHtml(c.explanation)}</p>` : ''}
            </div>
        </div>
    `).join('');

    const rewrites = (data.rewrite_suggestions || []).map(r => `
        <div class="border rounded-xl overflow-hidden" style="border-color:#E2E8F0;">
            <div class="px-4 py-2" style="background:#F8FAFC;"><span class="text-xs font-semibold" style="color:#1D6FB8;">📍 ${escapeHtml(r.location || 'Emplacement non spécifié')}</span></div>
            <div class="p-5 space-y-4">
                <div class="p-4 rounded-lg" style="background:#FEF2F2; border-left:3px solid #E24B4A;"><p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#E24B4A;">📝 Version originale</p><p class="text-sm italic" style="color:#475569;">« ${escapeHtml(r.original_text || 'Non spécifié')} »</p></div>
                <div class="p-4 rounded-lg" style="background:#F0FDF4; border-left:3px solid #22C55E;"><p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#22C55E;">✨ Version réécrite</p><p class="text-sm" style="color:#475569;">${escapeHtml(r.rewritten_text || 'Non spécifié')}</p></div>
                ${r.improvement_reason ? `<div class="bg-blue-50 p-3 rounded-lg"><p class="text-xs mb-1" style="color:#1D6FB8;">💡 Pourquoi cette réécriture est meilleure :</p><p class="text-xs" style="color:#475569;">${escapeHtml(r.improvement_reason)}</p></div>` : ''}
            </div>
        </div>
    `).join('');

    const style = data.style_analysis || {};
    const coherence = data.coherence_analysis || {};
    const authorStyle = data.author_style_check || {};
    const globalAssess = data.global_assessment || {};
    const recommendations = data.recommendations_for_author || [];

    return `
        <div class="grid grid-cols-1 lg:grid-cols-[200px_1fr] gap-6">
            <div class="bg-white rounded-2xl p-7 flex flex-col items-center text-center hover-lift" style="border:1px solid #E2E8F0;">
                <p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-4" style="color:#1D6FB8;">Score global</p>
                <div class="relative mb-3">
                    <svg width="110" height="110" viewBox="0 0 110 110">
                        <circle cx="55" cy="55" r="46" stroke="#EEF2F7" stroke-width="7" fill="none"/>
                        <circle cx="55" cy="55" r="46" stroke="#1D6FB8" stroke-width="7" fill="none" stroke-dasharray="289" stroke-dashoffset="${dashOffset}" stroke-linecap="round" transform="rotate(-90 55 55)"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="font-serif text-[2.2rem] font-bold leading-none" style="color:#0D3B64;">${score}</span>
                        <span class="text-xs" style="color:#94A3B8;">/100</span>
                    </div>
                </div>
                <span class="inline-block text-xs font-bold px-4 py-1.5 rounded-full" style="background:${badgeBg}; color:${badgeColor};">${badgeLabel}</span>
                ${data.analysis_metadata ? `<div class="mt-4 pt-4 text-center" style="border-top:1px solid #E2E8F0;"><p class="text-[10px]" style="color:#94A3B8;">📄 ${data.analysis_metadata.document_size || 0} caractères analysés ${data.analysis_metadata.was_truncated ? '<span class="inline-block ml-1 px-1.5 py-0.5 rounded" style="background:#FEF3C7; color:#D97706;">Tronqué</span>' : ''}</p></div>` : ''}
            </div>
            <div class="flex flex-col gap-4">
                <div class="bg-white rounded-2xl p-6 md:p-7 flex-1 hover-lift" style="border:1px solid #E2E8F0;">
                    <p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-3" style="color:#1D6FB8;">Résumé</p>
                    <p class="text-sm leading-relaxed" style="color:#334155;">${escapeHtml(data.summary || 'Aucun résumé disponible.')}</p>
                </div>
                <div class="bg-white rounded-2xl p-6 md:p-7 flex-1 hover-lift" style="border:1px solid #E2E8F0;">
                    <p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-3" style="color:#1D6FB8;">Conclusion</p>
                    <p class="text-sm leading-relaxed" style="color:#334155;">${escapeHtml(data.conclusion || 'Aucune conclusion disponible.')}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl p-7 hover-lift" style="border:1px solid #E2E8F0;">
                <div class="flex items-center gap-2.5 mb-5"><div class="w-2.5 h-2.5 rounded-full" style="background:#639922;"></div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase" style="color:#639922;">Points forts</p></div>
                <h3 class="font-serif text-lg font-semibold mb-1" style="color:#0D3B64;">Ce qui fonctionne</h3>
                <div class="accent-bar mb-5" style="background:#639922;"></div>
                <ul class="space-y-3">${strengthsList || '<li class="text-sm text-gray-500">Aucun point fort détecté.</li>'}</ul>
            </div>
            <div class="bg-white rounded-2xl p-7 hover-lift" style="border:1px solid #E2E8F0;">
                <div class="flex items-center gap-2.5 mb-5"><div class="w-2.5 h-2.5 rounded-full" style="background:#E24B4A;"></div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase" style="color:#E24B4A;">Points à améliorer</p></div>
                <h3 class="font-serif text-lg font-semibold mb-1" style="color:#0D3B64;">À retravailler</h3>
                <div class="accent-bar mb-5" style="background:#E24B4A;"></div>
                <ul class="space-y-4">${weaknessesList || '<li class="text-sm text-gray-500">Aucun point d\'amélioration détecté.</li>'}</ul>
            </div>
        </div>

        ${(data.detailed_corrections?.length || data.rewrite_suggestions?.length || coherence.temporal_issues?.length || coherence.character_consistency?.length) ? `
        <div class="bg-white rounded-2xl p-7 md:p-9 hover-lift" style="border:1px solid #E2E8F0;">
            <div class="flex items-center gap-2.5 mb-5"><div class="w-2.5 h-2.5 rounded-full" style="background:#E24B4A;"></div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase" style="color:#E24B4A;">Corrections précises</p></div>
            <h3 class="font-serif text-xl font-semibold mb-1" style="color:#0D3B64;">Avant / Après</h3>
            <div class="accent-bar mb-6" style="background:#E24B4A;"></div>
            <p class="text-sm mb-6" style="color:#64748B;">Pour chaque problème détecté, voici la phrase originale et la correction proposée :</p>
            ${data.detailed_corrections?.length ? `<div class="space-y-4 mb-8"><h4 class="font-bold text-sm uppercase tracking-wider" style="color:#1D6FB8;">✍️ Suggestions d'amélioration</h4>${corrections}</div>` : ''}
            ${data.rewrite_suggestions?.length ? `<div class="space-y-4 mb-8"><h4 class="font-bold text-sm uppercase tracking-wider" style="color:#1D6FB8;">✨ Réécritures proposées</h4>${rewrites}</div>` : ''}
            ${coherence.temporal_issues?.length ? `<div class="space-y-4 mb-8"><h4 class="font-bold text-sm uppercase tracking-wider" style="color:#BA7517;">⏱️ Incohérences temporelles</h4>${coherence.temporal_issues.map(i => `
                <div class="border rounded-xl overflow-hidden" style="border-color:#F3E1BC;">
                    <div class="px-4 py-2" style="background:#FFFBF3;"><span class="text-xs" style="color:#BA7517;">📍 ${escapeHtml(i.location || 'Emplacement non spécifié')}</span></div>
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="p-3 rounded-lg" style="background:#FEF2F2;"><p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color:#E24B4A;">❌ Problème</p><p class="text-sm italic" style="color:#475569;">« ${escapeHtml(i.original_text || 'Non spécifié')} »</p></div>
                            <div class="p-3 rounded-lg" style="background:#F0FDF4;"><p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color:#22C55E;">✅ Correction suggérée</p><p class="text-sm" style="color:#475569;">${escapeHtml(i.suggestion || 'Non spécifié')}</p></div>
                        </div>
                        ${i.reason ? `<p class="text-xs" style="color:#E24B4A;">⚠️ ${escapeHtml(i.reason)}</p>` : ''}
                    </div>
                </div>`).join('')}</div>` : ''}
            ${coherence.character_consistency?.length ? `<div class="space-y-4 mb-8"><h4 class="font-bold text-sm uppercase tracking-wider" style="color:#1D6FB8;">👤 Cohérence des personnages</h4>${coherence.character_consistency.map(i => `
                <div class="border rounded-xl overflow-hidden" style="border-color:#BDDCF5;">
                    <div class="px-4 py-2" style="background:#F0F7FF;"><span class="text-xs" style="color:#1D6FB8;">📍 ${escapeHtml(i.location || 'Emplacement non spécifié')}</span></div>
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="p-3 rounded-lg" style="background:#FEF2F2;"><p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color:#E24B4A;">❌ Problème</p><p class="text-sm italic" style="color:#475569;">« ${escapeHtml(i.original_text || 'Non spécifié')} »</p></div>
                            <div class="p-3 rounded-lg" style="background:#F0FDF4;"><p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color:#22C55E;">✅ Correction suggérée</p><p class="text-sm" style="color:#475569;">${escapeHtml(i.suggestion || 'Non spécifié')}</p></div>
                        </div>
                        ${i.reason ? `<p class="text-xs" style="color:#64748B;">💡 ${escapeHtml(i.reason)}</p>` : ''}
                    </div>
                </div>`).join('')}</div>` : ''}
            ${coherence.unresolved_points?.length ? `<div class="mt-6 rounded-2xl p-5" style="background:#FEF2F2; border:1px solid #FCA5A5;"><p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-4" style="color:#E24B4A;">Points non résolus</p><div class="grid grid-cols-1 md:grid-cols-2 gap-3">${coherence.unresolved_points.map(u => `<div class="flex flex-col gap-1 text-sm" style="color:#334155;"><div class="flex items-start gap-3"><span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#FCEBEB; color:#E24B4A;">✗</span><span>${escapeHtml(typeof u === 'object' ? (u.description || u) : u)}</span></div>${typeof u === 'object' && u.location ? `<p class="text-xs ml-8" style="color:#94A3B8;">📍 ${escapeHtml(u.location)}</p>` : ''}${typeof u === 'object' && u.suggestion ? `<p class="text-xs ml-8 mt-1" style="color:#639922;">💡 Suggestion : ${escapeHtml(u.suggestion)}</p>` : ''}</div>`).join('')}</div></div>` : ''}
        </div>` : ''}

        ${recommendations.length ? `
        <div class="bg-white rounded-2xl p-7 md:p-9 hover-lift" style="border:1px solid #E2E8F0;">
            <div class="flex items-center gap-2.5 mb-5"><div class="w-2.5 h-2.5 rounded-full" style="background:#BA7517;"></div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase" style="color:#BA7517;">Recommandations personnalisées</p></div>
            <h3 class="font-serif text-xl font-semibold mb-1" style="color:#0D3B64;">Conseils pour l'auteur</h3>
            <div class="accent-bar mb-6" style="background:#BA7517;"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">${recommendations.map(rec => `<div class="p-4 rounded-xl" style="background:#FFFBF3; border:1px solid #F3E1BC;"><div class="flex items-start gap-3"><span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#FAEEDA; color:#BA7517;">→</span><div><p class="text-sm leading-relaxed mb-2" style="color:#334155;">${escapeHtml(typeof rec === 'object' ? (rec.suggestion || rec.issue || '') : rec)}</p>${typeof rec === 'object' && rec.location ? `<p class="text-xs" style="color:#94A3B8;">📍 ${escapeHtml(rec.location)}</p>` : ''}${typeof rec === 'object' && rec.impact ? `<p class="text-xs mt-1" style="color:#639922;">📈 Impact : ${escapeHtml(rec.impact)}</p>` : ''}</div></div></div>`).join('')}</div>
        </div>` : ''}

        ${Object.keys(style).length ? `
        <div class="bg-white rounded-2xl p-7 md:p-9 hover-lift" style="border:1px solid #E2E8F0;">
            <div class="flex items-center gap-2.5 mb-5"><div class="w-2.5 h-2.5 rounded-full" style="background:#1D6FB8;"></div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase" style="color:#1D6FB8;">Analyse stylistique</p></div>
            <h3 class="font-serif text-xl font-semibold mb-1" style="color:#0D3B64;">Détail du style</h3>
            <div class="accent-bar mb-8"></div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="rounded-2xl p-5 text-center" style="background:#F8FAFC; border:1px solid #E2E8F0;"><div class="font-serif mb-1" style="font-size:2rem; color:#0D3B64; font-weight:700;">${style.fluidity || 0}<span style="font-size:1rem; color:#94A3B8;">/10</span></div><p class="text-xs font-medium mb-3" style="color:#64748B;">Fluidité</p><div class="progress-track"><div class="progress-fill" style="width:${(style.fluidity || 0) * 10}%;"></div></div></div>
                <div class="rounded-2xl p-5 text-center" style="background:#F8FAFC; border:1px solid #E2E8F0;"><div class="font-serif mb-1" style="font-size:2rem; color:#0D3B64; font-weight:700;">${style.vocabulary || 0}<span style="font-size:1rem; color:#94A3B8;">/10</span></div><p class="text-xs font-medium mb-3" style="color:#64748B;">Vocabulaire</p><div class="progress-track"><div class="progress-fill" style="width:${(style.vocabulary || 0) * 10}%;"></div></div></div>
                <div class="rounded-2xl p-5 text-center" style="background:#F8FAFC; border:1px solid #E2E8F0;"><div class="font-serif mb-1" style="font-size:2rem; color:#0D3B64; font-weight:700;">${style.sentence_variety || 0}<span style="font-size:1rem; color:#94A3B8;">/10</span></div><p class="text-xs font-medium mb-3" style="color:#64748B;">Variété des phrases</p><div class="progress-track"><div class="progress-fill" style="width:${(style.sentence_variety || 0) * 10}%;"></div></div></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-4" style="color:#639922;">Points forts stylistiques</p><ul class="space-y-3">${(style.strong_points || []).map(s => `<li class="flex items-start gap-3 text-sm pb-3" style="border-bottom:1px solid #F1F5F9;"><span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#EAF3DE; color:#639922;">✓</span>${escapeHtml(s)}</li>`).join('') || '<li class="text-sm text-gray-500">Aucun point fort stylistique détecté.</li>'}</ul></div>
                <div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-4" style="color:#BA7517;">Améliorations proposées</p><ul class="space-y-3">${(style.improvements || []).map(imp => `<li class="flex items-start gap-3 text-sm pb-3" style="border-bottom:1px solid #F1F5F9;"><span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold" style="background:#FAEEDA; color:#BA7517;">→</span>${escapeHtml(typeof imp === 'object' ? (imp.suggestion || '') : imp)}</li>`).join('') || '<li class="text-sm text-gray-500">Aucune amélioration stylistique proposée.</li>'}</ul></div>
            </div>
        </div>` : ''}

        ${Object.keys(authorStyle).length ? `
        <div class="bg-white rounded-2xl p-7 md:p-9 hover-lift" style="border:1px solid #E2E8F0;">
            <div class="flex items-center gap-2.5 mb-5"><div class="w-2.5 h-2.5 rounded-full" style="background:#1D6FB8;"></div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase" style="color:#1D6FB8;">Style auteur</p></div>
            <h3 class="font-serif text-xl font-semibold mb-1" style="color:#0D3B64;">Vérification du style auteur</h3>
            <div class="accent-bar mb-8"></div>
            <div class="grid grid-cols-1 lg:grid-cols-[160px_1fr] gap-8 items-start">
                <div class="flex flex-col items-center text-center">
                    <div class="relative"><svg width="100" height="100" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" stroke="#EEF2F7" stroke-width="6" fill="none"/><circle cx="50" cy="50" r="40" stroke="#1D6FB8" stroke-width="6" fill="none" stroke-dasharray="251" stroke-dashoffset="${251 - (251 * ((authorStyle.consistency_score || 75) / 100))}" stroke-linecap="round" transform="rotate(-90 50 50)"/></svg><div class="absolute inset-0 flex flex-col items-center justify-center"><span class="font-serif text-[1.8rem] font-bold" style="color:#0D3B64;">${authorStyle.consistency_score || 75}</span><span class="text-[10px]" style="color:#94A3B8;">cohérence</span></div></div>
                    <p class="text-xs font-medium mt-2" style="color:#64748B;">/100</p>
                    ${authorStyle.style_respected !== undefined ? `<span class="inline-block text-[10px] font-bold px-2 py-1 rounded-full mt-2" style="background:${authorStyle.style_respected ? '#EAF3DE' : '#FCEBEB'}; color:${authorStyle.style_respected ? '#3B6D11' : '#E24B4A'};">${authorStyle.style_respected ? 'Style respecté' : 'Style non respecté'}</span>` : ''}
                </div>
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-4 pb-4" style="border-bottom:1px solid #F1F5F9;"><p class="text-xs font-bold uppercase tracking-wider flex-shrink-0 sm:w-36" style="color:#64748B;">Style observé</p><p class="text-sm leading-relaxed" style="color:#334155;">${escapeHtml(authorStyle.summary || 'Analyse du style non disponible')}</p></div>
                    ${authorStyle.observed_style_traits?.length ? `<div class="flex flex-col sm:flex-row gap-1 sm:gap-4 pb-4" style="border-bottom:1px solid #F1F5F9;"><p class="text-xs font-bold uppercase tracking-wider flex-shrink-0 sm:w-36" style="color:#64748B;">Traits observés</p><p class="text-sm leading-relaxed" style="color:#334155;">${escapeHtml(authorStyle.observed_style_traits.join(', '))}</p></div>` : ''}
                </div>
            </div>
        </div>` : ''}

        ${Object.keys(globalAssess).length ? `
        <div class="bg-white rounded-2xl p-7 md:p-9 hover-lift" style="border:1px solid #E2E8F0;">
            <div class="flex items-center gap-2.5 mb-5"><div class="w-2.5 h-2.5 rounded-full" style="background:#1D6FB8;"></div><p class="text-[0.6rem] font-bold tracking-[3px] uppercase" style="color:#1D6FB8;">Évaluation globale</p></div>
            <h3 class="font-serif text-xl font-semibold mb-1" style="color:#0D3B64;">Analyse par parties</h3>
            <div class="accent-bar mb-8"></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="text-center p-4 rounded-xl" style="background:#F8FAFC;"><p class="text-xs text-gray-500 mb-1">Début</p><p class="text-2xl font-bold" style="color:#1D6FB8;">${globalAssess.beginning_quality || 0}/10</p></div>
                <div class="text-center p-4 rounded-xl" style="background:#F8FAFC;"><p class="text-xs text-gray-500 mb-1">Milieu</p><p class="text-2xl font-bold" style="color:#BA7517;">${globalAssess.middle_quality || 0}/10</p></div>
                <div class="text-center p-4 rounded-xl" style="background:#F8FAFC;"><p class="text-xs text-gray-500 mb-1">Fin</p><p class="text-2xl font-bold" style="color:#639922;">${globalAssess.ending_quality || 0}/10</p></div>
            </div>
            ${globalAssess.overall_progression ? `<p class="text-sm mb-3" style="color:#334155;">📈 ${escapeHtml(globalAssess.overall_progression)}</p>` : ''}
            ${globalAssess.biggest_strength ? `<p class="text-sm mb-2" style="color:#639922;">⭐ Plus grand point fort : ${escapeHtml(globalAssess.biggest_strength)}</p>` : ''}
            ${globalAssess.biggest_weakness ? `<p class="text-sm" style="color:#E24B4A;">⚠️ Plus grande faiblesse : ${escapeHtml(globalAssess.biggest_weakness)}</p>` : ''}
        </div>` : ''}
    `;
}

// ─────────────────────────────────────────────────────────────
// Paiement
// ─────────────────────────────────────────────────────────────
async function initiatePayment() {
    try {
        const resp = await fetch(PAYMENT_INIT_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ manuscript_id: MANUSCRIPT_ID, format: 'pdf' })
        });
        const result = await resp.json();
        if (result.success && result.payment_url) {
            window.location.href = result.payment_url;
        } else {
            alert('Erreur : ' + (result.error || 'Impossible de lancer le paiement'));
        }
    } catch (err) {
        alert('Erreur technique : ' + err.message);
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Lancer le chargement
loadAnalysis();
</script>
@endpush
@endsection