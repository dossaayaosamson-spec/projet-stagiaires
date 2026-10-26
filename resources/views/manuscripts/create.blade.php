@extends('layouts.app')

@section('content')

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .btn-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }
    /* Rendre le bouton plus cliquable */
    #submitBtn {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #submitBtn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px rgba(29,111,184,0.35);
    }
</style>

{{-- HERO --}}
<section style="background: linear-gradient(135deg, #0D3B64 0%, #1D6FB8 100%);" class="py-10 md:py-15 px-[6%] relative overflow-hidden">
  <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-10" style="background:#5BC4F5;"></div>
  <div class="absolute bottom-0 left-[40%] w-64 h-64 rounded-full opacity-5" style="background:#fff;"></div>
  <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
    <div class="max-w-2xl">
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-[5px] mb-6"
           style="background:rgba(91,196,245,0.15); border:1px solid rgba(91,196,245,0.35);">
        <span class="text-[0.62rem] font-bold tracking-[2.5px] uppercase" style="color:#7EC8F0;">Plateforme Éditoriale</span>
      </div>
      <h1 class="font-serif text-white text-[2.2rem] md:text-[3rem] leading-[1.25] mb-5 font-semibold">
        Analyse de votre<br>
        <em class="not-italic" style="color:#5BC4F5;">manuscrit</em>
      </h1>
      <p class="text-[1rem] md:text-[1.1rem] leading-[1.8] font-light max-w-lg font-serif italic" style="color:rgba(255,255,255,0.75);">
        Soumettez votre document pour recevoir une analyse approfondie.
      </p>
    </div>
    <div>
      <a href="{{ route('manuscripts.auteur') }}"
         class="inline-flex items-center gap-3 px-5 py-3 rounded-full text-sm font-semibold transition-all duration-300 hover:scale-105"
         style="background:rgba(255,255,255,0.15); backdrop-filter:blur(4px); border:1px solid rgba(255,255,255,0.25); color:white;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 6v4l3 3M8 12h4" stroke="currentColor" stroke-linecap="round"/>
        </svg>
        Assistant IA - Réécriture & Relecture
      </a>
    </div>
  </div>
</section>


{{-- ─────────────── FORM ─────────────── --}}
<div class="px-[5%] md:px-[8%] py-[60px] md:py-[80px]">

  <form action="{{ route('manuscripts.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-[55%_45%] gap-8">

      {{-- ── LEFT ── --}}
      <div class="bg-white rounded-2xl p-8 md:p-10 flex flex-col gap-8" style="border:1px solid #E2E8F0;">
        <div>
          <p class="text-[0.62rem] font-bold tracking-[3px] uppercase mb-6" style="color:#1D6FB8;">Informations du manuscrit</p>
          <h2 class="font-serif text-[1.6rem] font-semibold mb-1" style="color:#0D3B64;">Votre œuvre</h2>
          <div class="accent-bar mb-7"></div>
          <div class="mb-5">
            <label class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Titre du manuscrit</label>
            <input type="text" name="title" value="{{ old('title') }}" placeholder="Ex : Le mystère des Outre-Mer"
                   class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all"
                   style="border:1.5px solid #E2E8F0; background:#F8FAFC;">
            <p class="mt-1.5 text-xs" style="color:#94A3B8;">Optionnel, recommandé pour le suivi</p>
          </div>
          <div>
            <label class="block text-sm font-semibold mb-2" style="color:#1A2A3A;">Nom de l'auteur</label>
            <input type="text" name="author_name" value="{{ old('author_name') }}" placeholder="Ex : Marie Dupont"
                   class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#1D6FB8] transition-all"
                   style="border:1.5px solid #E2E8F0; background:#F8FAFC;">
            <p class="mt-1.5 text-xs" style="color:#94A3B8;">Optionnel — analyse anonyme si non renseigné</p>
          </div>
        </div>
        <div style="height:1px; background:#E2E8F0;"></div>
        <div>
          <p class="text-[0.62rem] font-bold tracking-[3px] uppercase mb-5" style="color:#1D6FB8;">Ce que vous recevrez</p>
          <ul class="space-y-3">
            @foreach([
              'Résumé automatique du document',
              'Score de qualité global',
              'Points forts et axes d\'amélioration',
              'Analyse stylistique détaillée',
              'Vérification de cohérence narrative',
              'Recommandations personnalisées',
            ] as $feature)
            <li class="flex items-center gap-3 text-sm" style="color:#475569;">
              <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"
                   style="background:#EEF5FD; border:1px solid #BDDCF5;">
                <svg width="9" height="9" viewBox="0 0 10 10" fill="none"><path d="M2 5.5l2 2 4-4" stroke="#1D6FB8" stroke-width="1.5"/></svg>
              </div>
              {{ $feature }}
            </li>
            @endforeach
          </ul>
        </div>
        @if ($errors->any())
        <div class="rounded-xl px-5 py-4" style="background:#FEF2F2; border:1px solid #FCA5A5;">
          <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#991B1B;">Erreurs à corriger</p>
          <ul class="space-y-1">
            @foreach ($errors->all() as $error)
            <li class="text-sm flex items-start gap-2" style="color:#B91C1C;">· {{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
      </div>

      {{-- ── RIGHT ── --}}
      <div class="flex flex-col gap-6">
        {{-- Upload card (inchangé) --}}
        <div class="bg-white rounded-2xl p-8 md:p-10 flex flex-col gap-6" style="border:1px solid #E2E8F0;">
          <div>
            <p class="text-[0.62rem] font-bold tracking-[3px] uppercase mb-1" style="color:#1D6FB8;">Votre document</p>
            <h2 class="font-serif text-[1.6rem] font-semibold mb-1" style="color:#0D3B64;">Importer le fichier</h2>
            <div class="accent-bar"></div>
          </div>

          <input type="file" name="document" id="fileInput" accept=".pdf,.docx,.txt" required class="sr-only">

          <div id="dropZone"
               class="flex flex-col items-center justify-center gap-4 rounded-2xl cursor-pointer transition-all duration-300 py-12 px-6 text-center border-2 border-dashed border-gray-300 bg-gray-50 hover:border-[#1D6FB8] hover:bg-blue-50 hover:shadow-md">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center bg-blue-50 border border-blue-200">
              <svg width="28" height="28" viewBox="0 0 40 40" fill="none"><rect x="6" y="4" width="22" height="28" rx="3" stroke="#1D6FB8" stroke-width="1.5"/><path d="M22 4v8h8" stroke="#1D6FB8" stroke-width="1.5"/><path d="M29 30v6M26 33l3-3 3 3" stroke="#1D6FB8" stroke-width="1.5"/></svg>
            </div>
            <div>
              <p class="text-sm font-semibold mb-1" style="color:#1A2A3A;">Glissez votre fichier ici</p>
              <p class="text-sm" style="color:#94A3B8;">ou <span id="browseLink" class="font-semibold underline underline-offset-2 cursor-pointer text-[#1D6FB8]">parcourez vos fichiers</span></p>
              <p class="text-xs mt-2 text-gray-400">PDF, DOCX, TXT — max 20 Mo</p>
            </div>
          </div>

          <div id="fileSelected" class="hidden items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 border border-blue-200">
            <div class="w-8 h-8 rounded-lg bg-[#1D6FB8] flex items-center justify-center">
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><rect x="2" y="1" width="10" height="13" rx="1.5" stroke="white" stroke-width="1.2"/><path d="M10 1v4h4" stroke="white" stroke-width="1.2"/></svg>
            </div>
            <span id="fileName" class="flex-1 text-sm font-medium truncate" style="color:#1A2A3A;"></span>
            <button type="button" id="fileRemove" class="text-gray-400 hover:text-red-500 text-xl leading-none">&times;</button>
          </div>

          <div class="grid grid-cols-2 gap-4 pt-4" style="border-top:1px solid #E2E8F0;">
            <div>
              <p class="flex items-center gap-1.5 text-xs font-bold mb-1" style="color:#1D6FB8;"><svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 1L2 4v4c0 3.5 2.5 6.5 6 7.5C12 14.5 14 11.5 14 8V4L8 1z" stroke="currentColor" stroke-width="1.3"/><path d="M5.5 8l1.8 1.8 3.2-3.2" stroke="currentColor" stroke-width="1.3"/></svg> Confidentiel</p>
              <p class="text-xs" style="color:#94A3B8;">Supprimé après analyse</p>
            </div>
            <div>
              <p class="flex items-center gap-1.5 text-xs font-bold mb-1" style="color:#1D6FB8;"><svg width="12" height="12" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3.5l2 1.5" stroke="currentColor" stroke-width="1.3"/></svg> ~30 secondes</p>
              <p class="text-xs" style="color:#94A3B8;">Résultats immédiats</p>
            </div>
          </div>
        </div>

        {{-- Submit button (amélioré, sans modal) --}}
        <div class="bg-white rounded-2xl p-8 flex flex-col gap-3" style="border:1px solid #E2E8F0;">
          <button type="submit" id="submitBtn"
            class="w-full flex items-center justify-center gap-2.5 py-4 px-8 rounded-xl text-white text-[0.75rem] font-bold tracking-[1.5px] uppercase transition-all duration-300 cursor-pointer"
            style="background:#1D6FB8; box-shadow:0 4px 16px rgba(29,111,184,0.30);"
            onmouseover="this.style.background='#1560A0'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(29,111,184,0.40)';"
            onmouseout="this.style.background='#1D6FB8'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(29,111,184,0.30)';"
            onclick="this.innerHTML = '<span class=\'btn-spinner\'></span> Analyse en cours...'; this.disabled = true; this.form.submit();">
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
              <path d="M8 2l1.5 4.5H14l-3.7 2.7 1.4 4.3L8 11l-3.7 2.5 1.4-4.3L2 6.5h4.5L8 2z" fill="white"/>
            </svg>
            Lancer l'analyse
          </button>
          <p class="text-xs text-center" style="color:#94A3B8;">Analyse gratuite · Aucune inscription requise</p>
        </div>
      </div>
    </div>
  </form>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
    @foreach([
      ['icon'=>'<path d="M10 2a8 8 0 100 16A8 8 0 0010 2z" stroke="#1D6FB8" stroke-width="1.4"/><path d="M7 10l2 2 4-4" stroke="#1D6FB8" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>','label'=>'Technologie','title'=>'Analyse experte','desc'=>'Traitement par les modèles de langage les plus avancés pour une évaluation précise.'],
      ['icon'=>'<rect x="3" y="3" width="14" height="14" rx="3" stroke="#1D6FB8" stroke-width="1.4"/><path d="M7 10h6M10 7v6" stroke="#1D6FB8" stroke-width="1.4" stroke-linecap="round"/>','label'=>'Résultats','title'=>'Rapport détaillé','desc'=>'Score global, points forts, axes d\'amélioration et recommandations concrètes.'],
      ['icon'=>'<path d="M10 2L3 6v5c0 4 3 7 7 8 4-1 7-4 7-8V6L10 2z" stroke="#1D6FB8" stroke-width="1.4"/>','label'=>'Sécurité','title'=>'Confidentialité totale','desc'=>'Vos fichiers sont chiffrés en transit et supprimés automatiquement après l\'analyse.'],
    ] as $card)
    <div class="bg-white rounded-2xl p-7 hover-lift" style="border:1px solid #E2E8F0;">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background:#EEF5FD; border:1px solid #BDDCF5;">
        <svg width="18" height="18" viewBox="0 0 20 20" fill="none">{!! $card['icon'] !!}</svg>
      </div>
      <p class="text-[0.6rem] font-bold tracking-[3px] uppercase mb-1" style="color:#1D6FB8;">{{ $card['label'] }}</p>
      <h3 class="font-serif text-lg font-semibold mb-1" style="color:#0D3B64;">{{ $card['title'] }}</h3>
      <div class="accent-bar mb-4"></div>
      <p class="text-sm leading-relaxed" style="color:#64748B;">{{ $card['desc'] }}</p>
    </div>
    @endforeach
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const dropZone = document.getElementById('dropZone');
  const fileInput = document.getElementById('fileInput');
  const browseLink = document.getElementById('browseLink');
  const fileSelected = document.getElementById('fileSelected');
  const fileName = document.getElementById('fileName');
  const fileRemove = document.getElementById('fileRemove');

  if (dropZone && fileInput) {
    dropZone.addEventListener('click', (e) => { if (!e.target.closest('#browseLink')) fileInput.click(); });
    browseLink?.addEventListener('click', (e) => { e.preventDefault(); fileInput.click(); });
    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('drag-active'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-active'));
    dropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      dropZone.classList.remove('drag-active');
      if (e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0]);
    });
    fileInput.addEventListener('change', () => { if (fileInput.files[0]) handleFile(fileInput.files[0]); });
    fileRemove?.addEventListener('click', () => {
      fileInput.value = '';
      fileSelected.classList.add('hidden');
      dropZone.classList.remove('has-file');
    });
  }

  function handleFile(file) {
    if (file.size > 20 * 1024 * 1024) { alert('Fichier trop volumineux. Max 20 Mo.'); return; }
    const ext = '.' + file.name.split('.').pop().toLowerCase();
    if (!['.pdf','.docx','.txt'].includes(ext)) { alert('Format non supporté. PDF, DOCX ou TXT.'); return; }
    fileName.textContent = file.name;
    fileSelected.classList.remove('hidden');
    dropZone.classList.add('has-file');
  }
});
</script>
@endpush
@endsection