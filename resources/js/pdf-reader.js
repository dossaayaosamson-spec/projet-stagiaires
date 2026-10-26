// PDF Reader Logic for AGAU Editions
// Loaded via Vite in reader.blade.php

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('pdf-container');
    if (!container) return;

    // Configuration from data attributes
    const pdfUrl = container.dataset.pdfUrl;
    const bookId = container.dataset.bookId;
    const csrfToken = container.dataset.csrfToken;
    let lastSentProgress = parseInt(container.dataset.progress) || 0;

    // State
    let pdfDoc = null;
    let currentPage = 1;
    let totalPages = 0;
    let isRendering = false;
    let currentZoom = window.innerWidth < 768 ? 1.0 : 1.5;

    // PDF.js worker
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    } else {
        console.error('pdfjsLib is not defined. Make sure the script is loaded.');
        return;
    }

    // Elements
    const canvas = document.getElementById('pdf-canvas');
    const pageInfo = document.getElementById('page-info');
    const pageInfoBottom = document.getElementById('page-info-bottom');
    const progressPercent = document.getElementById('progress-percent');
    const prevBtns = document.querySelectorAll('.prev-btn');
    const nextBtns = document.querySelectorAll('.next-btn');

    // Charger le PDF
    pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
        pdfDoc = pdf;
        totalPages = pdf.numPages;
        const infoText = `Page 1 / ${totalPages}`;
        if (pageInfo) pageInfo.textContent = infoText;
        if (pageInfoBottom) pageInfoBottom.textContent = infoText;
        renderPage(currentPage);
    }).catch(err => {
        console.error('Erreur chargement PDF:', err);
    });

    // Afficher une page
    function renderPage(num) {
        if (isRendering || !pdfDoc) return;
        isRendering = true;

        pdfDoc.getPage(num).then(page => {
            const ctx = canvas.getContext('2d');
            
            // On calcule le viewport avec le zoom actuel
            const viewport = page.getViewport({ scale: currentZoom });

            canvas.width = viewport.width;
            canvas.height = viewport.height;

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            page.render(renderContext).promise.then(() => {
                isRendering = false;
                updateControls();
                updateProgress();
            });
        });
    }

    // Changer de page
    window.changePage = function(delta) {
        const newPage = currentPage + delta;
        if (newPage < 1 || newPage > totalPages) return;
        currentPage = newPage;
        
        const infoText = `Page ${currentPage} / ${totalPages}`;
        if (pageInfo) pageInfo.textContent = infoText;
        if (pageInfoBottom) pageInfoBottom.textContent = infoText;
        
        renderPage(currentPage);
        
        // Scroll to top of document smoothly
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Zoom
    window.changeZoom = function(delta) {
        currentZoom += delta;
        if (currentZoom < 0.5) currentZoom = 0.5;
        if (currentZoom > 4) currentZoom = 4;
        renderPage(currentPage);
    };

    window.resetZoom = function() {
        currentZoom = window.innerWidth < 768 ? 1.0 : 1.5;
        renderPage(currentPage);
    };

    // Plein écran
    window.toggleFullscreen = function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    };

    // Mettre à jour les boutons
    function updateControls() {
        prevBtns.forEach(btn => btn.disabled = currentPage <= 1);
        nextBtns.forEach(btn => btn.disabled = currentPage >= totalPages);
    }

    // Calculer et envoyer la progression
    function updateProgress() {
        if (totalPages === 0) return;
        const percent = Math.round((currentPage / totalPages) * 100);

        if (progressPercent) progressPercent.textContent = percent + '%';

        // Envoyer au serveur seulement si changement significatif
        if (Math.abs(percent - lastSentProgress) >= 5 || percent === 100) {
            lastSentProgress = percent;
            fetch("/progression/update", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ book_id: bookId, progress: percent }),
            });
        }
    }

    // Bloquer clic droit
    document.addEventListener('contextmenu', e => e.preventDefault());
    
    // Navigation au clavier
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowLeft') window.changePage(-1);
        if (e.key === 'ArrowRight') window.changePage(1);
    });
});
