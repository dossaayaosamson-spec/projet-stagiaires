@extends('layouts.app')

@section('title', 'Lecture : ' . $book->titre . ' — AGAU Éditions')

@section('content')
<style>
    body { 
        margin: 0; 
        background: #0F172A; 
        color: #F8FAFC;
        font-family: 'Montserrat', sans-serif;
    }

    nav, footer { display: none !important; }

    .reader-header {
        background: rgba(15, 23, 42, 0.95);
        backdrop-blur: 10px;
        color: white;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .reader-header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .reader-back {
        color: rgba(255,255,255,0.6);
        text-decoration: none;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: rgba(255,255,255,0.05);
        border-radius: 8px;
        transition: all 0.2s;
    }

    .reader-back:hover { 
        color: white; 
        background: rgba(255,255,255,0.1);
    }

    .reader-info h1 {
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
        color: #F8FAFC;
    }

    .reader-info p {
        font-size: 0.75rem;
        color: #94A3B8;
        margin: 2px 0 0 0;
    }

    .reader-controls {
        background: #1E293B;
        padding: 15px 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .reader-controls.bottom {
        border-bottom: none;
        border-top: 1px solid rgba(255,255,255,0.05);
        margin-top: 40px;
        padding: 40px 24px;
    }

    .reader-btn {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        font-weight: 600;
    }

    .reader-btn:hover:not(:disabled) {
        background: rgba(255,255,255,0.15);
        border-color: rgba(255,255,255,0.3);
    }

    .reader-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .reader-btn.primary {
        background: #1B6CA8;
        border: none;
    }

    .reader-btn.primary:hover:not(:disabled) {
        background: #2377B3;
    }

    #page-info, #page-info-bottom {
        font-size: 0.9rem;
        font-weight: 600;
        color: #F8FAFC;
        min-width: 120px;
        text-align: center;
    }

    .pdf-container {
        display: flex;
        justify-content: center;
        padding: 40px 20px;
        background: #0F172A;
        min-height: 60vh;
        overflow-x: auto;
    }

    #pdf-canvas {
        box-shadow: 0 30px 60px rgba(0,0,0,0.5);
        background: white;
        max-width: 100%;
        height: auto !important;
    }

    .zoom-controls {
        position: fixed;
        right: 30px;
        bottom: 40px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 1000;
    }

    .zoom-btn {
        width: 48px;
        height: 48px;
        background: #1B6CA8;
        border: none;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.4rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        transition: all 0.2s;
    }

    .zoom-btn:hover {
        background: #2377B3;
        transform: scale(1.1);
    }

    .reader-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .fullscreen-btn {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        padding: 8px;
        border-radius: 6px;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .reader-controls { padding: 12px; gap: 10px; }
        .reader-btn { padding: 8px 12px; font-size: 0.8rem; }
        .zoom-controls { right: 15px; bottom: 20px; }
        .zoom-btn { width: 40px; height: 40px; font-size: 1.1rem; }
    }
</style>

<div class="reader-header">
    <div class="reader-header-left">
        <a href="javascript:void(0)" onclick="window.history.back();" class="reader-back">
            <i class="bi bi-arrow-left"></i> Quitter
        </a>
        <div class="reader-info">
            <h1>{{ $book->titre }}</h1>
            <p>{{ $book->author->nom ?? '' }}</p>
        </div>
    </div>

    <div class="reader-actions">
        <button onclick="toggleFullscreen()" class="fullscreen-btn" title="Plein écran">
            <i class="bi bi-fullscreen"></i>
        </button>
        <div class="flex items-center bg-white/5 rounded-lg px-3 py-1 gap-2">
            <span class="text-[0.8rem] font-bold text-agau-blue" id="progress-percent">0%</span>
            <span class="text-[0.6rem] uppercase tracking-wider text-white/70">Lu</span>
        </div>
    </div>
</div>

@if($book->pdf_path)
    {{-- Contrôles Haut --}}
    <div class="reader-controls">
        <button class="reader-btn prev-btn" onclick="changePage(-1)" disabled>
            <i class="bi bi-chevron-left"></i> Précédent
        </button>
        <div id="page-info">Page 1 / —</div>
        <button class="reader-btn primary next-btn" onclick="changePage(1)">
            Suivant <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    {{-- Viewer PDF --}}
    <div class="pdf-container" id="pdf-container" 
         data-pdf-url="{{ route('read.pdf', $book->id) }}"
         data-book-id="{{ $book->id }}"
         data-csrf-token="{{ csrf_token() }}"
         data-progress="{{ $progress ? $progress->progress : 0 }}">
        <canvas id="pdf-canvas"></canvas>
    </div>

    {{-- Contrôles Bas --}}
    <div class="reader-controls bottom">
        <button class="reader-btn prev-btn" onclick="changePage(-1)" disabled>
            <i class="bi bi-chevron-left"></i> Précédent
        </button>
        <div id="page-info-bottom">Page 1 / —</div>
        <button class="reader-btn primary next-btn" onclick="changePage(1)">
            Suivant <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    {{-- Zoom Controls Floating --}}
    <div class="zoom-controls">
        <button class="zoom-btn" onclick="changeZoom(0.2)" title="Zoom avant">
            <i class="bi bi-plus-lg"></i>
        </button>
        <button class="zoom-btn" onclick="changeZoom(-0.2)" title="Zoom arrière">
            <i class="bi bi-dash-lg"></i>
        </button>
        <button class="zoom-btn" style="background: #475569;" onclick="resetZoom()" title="Réinitialiser">
            <i class="bi bi-arrows-angle-contract"></i>
        </button>
    </div>

    {{-- PDF.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @vite(['resources/js/pdf-reader.js'])

@else
    {{-- Pas de PDF --}}
    <div class="pdf-container">
        <div class="text-center py-20">
            <i class="bi bi-file-earmark-pdf text-6xl text-white/20 mb-6 block"></i>
            <h2 class="text-xl text-white/70 font-bold mb-2">PDF non disponible</h2>
            <p class="text-white/40">Le fichier de lecture n'a pas encore été mis en ligne.</p>
        </div>
    </div>
@endif

@endsection
