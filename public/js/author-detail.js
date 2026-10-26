// author-detail.js
// Version stable – reprend les endpoints réels de l’ancien infoauteur.js
const djangoApiUrl = 'https://agaueditions-ia.saeicubetech.com';
// Variables globales
let currentText = '';
let selectedFile = null;
let manuscriptFile = null;
let manuscriptFileContent = '';
let currentPaymentId = null;
let pollingInterval = null;
let currentTab = 'upload';       // pour la réécriture
let currentReviewTab = 'rl_paste'; // pour la relecture


// ==================== RÉÉCRITURE STYLISTIQUE ====================

let rewriteUploadedText = '';
let rewriteFullText = '';
let rewritePreviewText = '';
let rewriteCurrentPaymentId = null;

function switchRewriteTab(tab) {
    const pasteDiv = document.getElementById('rewrite_paste');
    const uploadDiv = document.getElementById('rewrite_upload');
    const pasteBtn = document.getElementById('rewriteTabPasteBtn');
    const uploadBtn = document.getElementById('rewriteTabUploadBtn');
    if (tab === 'rewrite_paste') {
        pasteDiv.classList.remove('hidden');
        uploadDiv.classList.add('hidden');
        pasteBtn.style.cssText = 'background:#EEF5FD;color:#1D6FB8;border-bottom:2px solid #1D6FB8;';
        uploadBtn.style.cssText = 'background:#F8FAFC;color:#94A3B8;border-bottom:none;';
    } else {
        pasteDiv.classList.add('hidden');
        uploadDiv.classList.remove('hidden');
        uploadBtn.style.cssText = 'background:#EEF5FD;color:#1D6FB8;border-bottom:2px solid #1D6FB8;';
        pasteBtn.style.cssText = 'background:#F8FAFC;color:#94A3B8;border-bottom:none;';
    }
}

// Compteur de mots pour le textarea de réécriture
const rewriteTextarea = document.getElementById('rewriteText');
if (rewriteTextarea) {
    rewriteTextarea.addEventListener('input', function() {
        const words = this.value.trim().split(/\s+/).filter(w => w.length > 0).length;
        document.getElementById('rewriteWordCount').innerText = words + ' mot' + (words > 1 ? 's' : '');
    });
}

// Gestion upload fichier réécriture
const rewriteDropZone = document.getElementById('rewriteDropZone');
const rewriteFileInput = document.getElementById('rewriteFileInput');
if (rewriteDropZone) {
    rewriteDropZone.addEventListener('click', () => rewriteFileInput.click());
    rewriteDropZone.addEventListener('dragover', (e) => { e.preventDefault(); rewriteDropZone.classList.add('drag-over'); });
    rewriteDropZone.addEventListener('dragleave', () => { rewriteDropZone.classList.remove('drag-over'); });
    rewriteDropZone.addEventListener('drop', handleRewriteFileDrop);
    rewriteFileInput.addEventListener('change', handleRewriteFileSelect);
}

async function handleRewriteFileDrop(e) {
    e.preventDefault();
    rewriteDropZone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) await processRewriteFile(file);
}

async function handleRewriteFileSelect(e) {
    const file = e.target.files[0];
    if (file) await processRewriteFile(file);
}

async function processRewriteFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    rewriteDropZone.classList.add('hidden');
    document.getElementById('rewriteFileInfo').classList.remove('hidden');
    document.getElementById('rewriteFileName').innerText = file.name;

    try {
        const response = await fetch(djangoApiUrl + '/api/extract-text/', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.text) {
            rewriteUploadedText = data.text;
            rewriteFullText = data.text;
            rewritePreviewText = truncateWords(data.text, 300);
        } else {
            alert('Erreur extraction texte');
        }
    } catch (err) {
        alert('Erreur de connexion');
    }
}

function clearRewriteFile() {
    rewriteUploadedText = '';
    rewriteFullText = '';
    rewritePreviewText = '';
    document.getElementById('rewriteFileInfo').classList.add('hidden');
    document.getElementById('rewriteFileName').innerText = '';
    rewriteFileInput.value = '';
    rewriteDropZone.classList.remove('hidden');
}

function truncateWords(text, limit) {
    const words = text.trim().split(/\s+/);
    if (words.length <= limit) return text;
    return words.slice(0, limit).join(' ') + '…';
}

function getRewriteSourceText() {
    const activeTab = document.querySelector('.rewrite-tab-content:not(.hidden)')?.id;
    if (activeTab === 'rewrite_paste') {
        const pasteText = document.getElementById('rewriteText').value;
        rewriteFullText = pasteText;
        rewritePreviewText = truncateWords(pasteText, 300);
        return { full: pasteText, preview: rewritePreviewText };
    } else {
        if (!rewriteUploadedText) {
            alert('Veuillez d’abord uploader un fichier.');
            return null;
        }
        return { full: rewriteFullText, preview: rewritePreviewText };
    }
}

async function previewRewrite() {
    const source = getRewriteSourceText();
    if (!source) return;
    const customStyle = document.getElementById('customStyleRewrite').value;
    const previewCard = document.getElementById('rewritePreviewCard');
    const contentDiv = document.getElementById('rewritePreviewContent');
    const badge = document.getElementById('previewWordCountBadge');

    previewCard.classList.remove('hidden');
    contentDiv.innerHTML = '<div class="text-center py-8"><div class="loading-spinner mx-auto mb-4"></div><p class="text-sm">L\'IA réécrit l\'aperçu...</p></div>';
    badge.innerText = '(extrait limité à 300 mots)';

    try {
        const response = await fetch(djangoApiUrl + '/api/rewrite-free/', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                text: source.preview,
                author_name: window.authorName,
                custom_style: customStyle
            })
        });
        const data = await response.json();
        if (data.success) {
            const r = data.result;
            let html = `<div class="mb-4 p-4 rounded-xl" style="background:#F0FDF4; border:1px solid #BBF7D0;">
                <div class="text-sm whitespace-pre-wrap">${escapeHtml(r.rewritten_text)}</div>
            </div>`;
            if (r.style_features && r.style_features.length) {
                html += `<div class="mt-4 p-3 rounded-xl" style="background:#EEF5FD;">
                    <p class="text-xs font-semibold mb-2" style="color:#0D3B64;">🎨 Caractéristiques appliquées</p>
                    <div class="flex flex-wrap gap-2">${r.style_features.map(f => `<span class="text-xs px-2 py-1 rounded-full bg-white">${escapeHtml(f)}</span>`).join('')}</div>
                </div>`;
            }
            if (r.explanation) {
                html += `<div class="mt-3 p-3 rounded-xl" style="background:#FEF9C3;"><p class="text-xs">💡 ${escapeHtml(r.explanation)}</p></div>`;
            }
            contentDiv.innerHTML = html;
        } else {
            contentDiv.innerHTML = `<p class="text-red-500">Erreur: ${data.error || 'Inconnue'}</p>`;
        }
    } catch (err) {
        contentDiv.innerHTML = `<p class="text-red-500">Erreur réseau</p>`;
    }
}

async function initiateRewritePayment() {
    const source = getRewriteSourceText();
    if (!source) return;
    const customStyle = document.getElementById('customStyleRewrite').value;
    const format = document.querySelector('input[name="rewriteFormat"]:checked').value;
    const email = document.getElementById('rewriteCustomerEmail').value.trim();

    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('modalContent').innerHTML = `<div class="text-center py-6"><div class="loading-spinner mx-auto mb-3"></div><p class="text-sm">Création de la transaction...</p></div>`;

    try {
        const response = await fetch(djangoApiUrl + '/api/rewrite-payment/initiate/', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRFToken': window.csrfToken },
            body: JSON.stringify({
                text: source.full,
                author_name: window.authorName,
                custom_style: customStyle,
                format: format,
                email: email
            })
        });
        const data = await response.json();
        if (data.success) {
            rewriteCurrentPaymentId = data.payment_id;
            window.open(data.payment_url, '_blank');
            closePaymentModal();
            alert("Vous allez être redirigé après le paiement.");
            // Commencer le polling
            startRewritePolling();
        } else {
            document.getElementById('modalContent').innerHTML = `<div class="text-center py-4"><p class="text-red-500">❌ ${escapeHtml(data.error)}</p><button onclick="closePaymentModal()" class="mt-4 px-4 py-2 rounded-xl">Fermer</button></div>`;
        }
    } catch (err) {
        document.getElementById('modalContent').innerHTML = `<div class="text-center py-4"><p class="text-red-500">❌ Erreur réseau</p><button onclick="closePaymentModal()" class="mt-4 px-4 py-2 rounded-xl">Fermer</button></div>`;
    }
}

let rewritePollingInterval = null;

function startRewritePolling() {
    if (rewritePollingInterval) clearInterval(rewritePollingInterval);
    rewritePollingInterval = setInterval(async () => {
        if (!rewriteCurrentPaymentId) return;
        try {
            const resp = await fetch(djangoApiUrl + `/api/rewrite-payment/${rewriteCurrentPaymentId}/status/`);
            const data = await resp.json();
            if (data.is_paid) {
                clearInterval(rewritePollingInterval);
                showRewritePaymentSuccess(data);
            }
        } catch(e) {}
    }, 5000);
}

async function showRewritePaymentSuccess(data) {
    const card = document.getElementById('rewritePaymentResultCard');
    const content = document.getElementById('rewritePaymentResultContent');
    if (data.has_result) {
        content.innerHTML = `
            <div class="text-center py-6">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#22C55E" stroke-width="2"/></svg>
                </div>
                <h4 class="font-serif text-xl font-semibold mb-2" style="color:#0D3B64;">Réécriture terminée ! 🎉</h4>
                <p class="text-sm mb-6">Votre texte réécrit est prêt.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="${djangoApiUrl}/rewrite/download/${data.payment_id}/?token=${data.token || ''}&format=pdf" class="px-6 py-3 rounded-xl font-semibold text-sm hover:opacity-90" style="background:#EF4444; color:#fff;" target="_blank">📄 Télécharger en PDF</a>
                    <a href="${djangoApiUrl}/rewrite/download/${data.payment_id}/?token=${data.token || ''}&format=docx" class="px-6 py-3 rounded-xl font-semibold text-sm hover:opacity-90" style="background:#1D6FB8; color:#fff;" target="_blank">📝 Télécharger en Word</a>
                </div>
                <p class="text-xs text-gray-500 mt-6">✅ Le téléchargement a été lancé. Une fois le fichier récupéré, vous pouvez fermer cette page.</p>
            </div>`;
    } else {
        content.innerHTML = `<div class="text-center py-8"><div class="loading-spinner mx-auto mb-4" style="border-top-color:#EAB308;"></div><p class="text-sm">Paiement confirmé ! L'IA finalise la réécriture…</p><p class="text-xs mt-1">Cette section se mettra à jour automatiquement.</p></div>`;
        rewritePollingInterval = setInterval(async () => {
            try {
                const r = await fetch(djangoApiUrl + `/api/rewrite-payment/${data.payment_id}/status/`);
                const d = await r.json();
                if (d.has_result) { clearInterval(rewritePollingInterval); showRewritePaymentSuccess(d); }
            } catch(e) {}
        }, 6000);
    }
    card.classList.remove('hidden');
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ═══════════════════════════════════════════════════════════════════════════
// 1. GESTION DES MANUSCRITS DE RÉFÉRENCE (MODAL)
// ═══════════════════════════════════════════════════════════════════════════

function switchManuscriptTab(tab) {
    const uploadDiv = document.getElementById('msTabUpload');
    const pasteDiv = document.getElementById('msTabPaste');
    const uploadBtn = document.getElementById('msTabUploadBtn');
    const pasteBtn = document.getElementById('msTabPasteBtn');
    
    if (tab === 'ms_upload') {
        uploadDiv.classList.remove('hidden');
        pasteDiv.classList.add('hidden');
        uploadBtn.style.cssText = 'background:#EEF5FD;color:#1D6FB8;border-bottom:2px solid #1D6FB8;';
        pasteBtn.style.cssText = 'background:#F8FAFC;color:#94A3B8;border-bottom:none;';
    } else {
        uploadDiv.classList.add('hidden');
        pasteDiv.classList.remove('hidden');
        pasteBtn.style.cssText = 'background:#EEF5FD;color:#1D6FB8;border-bottom:2px solid #1D6FB8;';
        uploadBtn.style.cssText = 'background:#F8FAFC;color:#94A3B8;border-bottom:none;';
    }
}

// Upload manuscrit (modal)
const msDropZone = document.getElementById('msDropZone');
const msFileInput = document.getElementById('msFileInput');
const msFileInfo = document.getElementById('msFileInfo');
const msFileName = document.getElementById('msFileName');
const msUploadedText = document.getElementById('msUploadedText');

if (msDropZone) {
    msDropZone.addEventListener('click', () => msFileInput.click());
    msDropZone.addEventListener('dragover', (e) => { e.preventDefault(); msDropZone.classList.add('drag-over'); });
    msDropZone.addEventListener('dragleave', () => { msDropZone.classList.remove('drag-over'); });
    msDropZone.addEventListener('drop', async (e) => {
        e.preventDefault();
        msDropZone.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) await processManuscriptFile(file);
    });
}
if (msFileInput) {
    msFileInput.addEventListener('change', async (e) => {
        if (e.target.files[0]) await processManuscriptFile(e.target.files[0]);
    });
}

async function processManuscriptFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    
    msDropZone.classList.add('hidden');
    msFileInfo.classList.remove('hidden');
    msFileName.textContent = file.name;
    manuscriptFile = file;
    
    const response = await fetch(djangoApiUrl + '/api/extract-text/', {
        method: 'POST',
        body: formData
    });
    const data = await response.json();
    if (data.text) {
        manuscriptFileContent = data.text;
        msUploadedText.value = data.text;
    }
}

function clearManuscriptFile() {
    manuscriptFile = null;
    manuscriptFileContent = '';
    msUploadedText.value = '';
    msFileInput.value = '';
    msFileInfo.classList.add('hidden');
    msDropZone.classList.remove('hidden');
}

// Ajout d'un manuscrit (avec indexation automatique)
function addManuscript(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    const isUpload = !document.getElementById('msTabUpload').classList.contains('hidden');
    let content = '';
    if (isUpload) {
        content = manuscriptFileContent || document.getElementById('msUploadedText').value;
    } else {
        content = document.getElementById('msPasteText').value;
    }
    
    if (!content || content.trim() === '') {
        alert('Veuillez ajouter le contenu du manuscrit');
        return;
    }
    
    const authorId = parseInt(window.authorId);
    if (isNaN(authorId) || authorId <= 0) {
        alert('Auteur invalide. Rechargez la page.');
        return;
    }
    
    fetch(djangoApiUrl + '/api/reference-manuscripts/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            author_id: authorId,
            title: formData.get('title'),
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            return fetch(djangoApiUrl + '/api/index-manuscript/' + data.manuscript_id + '/', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });
        } else {
            throw new Error(data.error || 'Erreur d\'ajout');
        }
    })
    .then(() => location.reload())
    .catch(error => { alert('Erreur: ' + error.message); });
}

function indexManuscript(manuscriptId) {
    fetch(djangoApiUrl + '/api/index-manuscript/' + manuscriptId + '/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
        else alert('Erreur lors de l\'indexation');
    });
}

function deleteManuscript(manuscriptId) {
    if (confirm('Voulez-vous vraiment supprimer ce manuscrit ?')) {
        fetch(djangoApiUrl + '/api/reference-manuscripts/' + manuscriptId + '/', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Erreur: ' + (data.error || 'Impossible de supprimer'));
        })
        .catch(error => { alert('Erreur: ' + error.message); });
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// 2. ENTRAÎNEMENT DE L'IA
// ═══════════════════════════════════════════════════════════════════════════

function trainAuthor() {
    const btn = document.getElementById('trainBtn');
    if (!btn) return;

    // Récupérer l'ID auteur (depuis window.authorId ou l'URL)
    let authorId = window.authorId;
    if (!authorId || authorId === 'null' || authorId === null) {
        const match = window.location.pathname.match(/\/auteurs\/(\d+)/);
        if (match) authorId = parseInt(match[1], 10);
    }
    if (!authorId) {
        alert('Impossible de déterminer l\'auteur. Rechargez la page.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '⏳ Étude du style...';

    fetch(djangoApiUrl + '/api/train-author/' + authorId + '/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + (data.error || 'Entraînement échoué'));
            btn.disabled = false;
            btn.innerHTML = '🧠 Entraîner l\'IA';
        }
    })
    .catch(error => {
        alert('Erreur réseau: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '🧠 Entraîner l\'IA';
    });
}

// ═══════════════════════════════════════════════════════════════════════════
// 3. RÉÉCRITURE STYLISTIQUE (ancienne version – à conserver)
// ═══════════════════════════════════════════════════════════════════════════

function switchTab(tab) {
    const uploadTab = document.getElementById('tabUpload');
    const pasteTab = document.getElementById('tabPaste');
    const uploadBtn = document.getElementById('tabUploadBtn');
    const pasteBtn = document.getElementById('tabPasteBtn');
    
    if (tab === 'upload') {
        uploadTab.classList.remove('hidden');
        pasteTab.classList.add('hidden');
        uploadBtn.style.cssText = 'background:#EEF5FD;color:#1D6FB8;border-bottom:2px solid #1D6FB8;';
        pasteBtn.style.cssText = 'background:#F8FAFC;color:#94A3B8;border-bottom:none;';
    } else {
        uploadTab.classList.add('hidden');
        pasteTab.classList.remove('hidden');
        pasteBtn.style.cssText = 'background:#EEF5FD;color:#1D6FB8;border-bottom:2px solid #1D6FB8;';
        uploadBtn.style.cssText = 'background:#F8FAFC;color:#94A3B8;border-bottom:none;';
    }
    currentTab = tab;
}

// Gestion du fichier upload pour réécriture
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');
const uploadedText = document.getElementById('uploadedText');

if (dropZone) {
    dropZone.addEventListener('click', () => fileInput.click());
    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('drag-over'); });
    dropZone.addEventListener('drop', async (e) => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) await processFile(file);
    });
}
if (fileInput) {
    fileInput.addEventListener('change', async (e) => {
        if (e.target.files[0]) await processFile(e.target.files[0]);
    });
}

async function processFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    
    dropZone.classList.add('hidden');
    fileInfo.classList.remove('hidden');
    fileName.textContent = file.name;
    selectedFile = file;
    
    try {
        const response = await fetch(djangoApiUrl + '/api/extract-text/', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.text) {
            currentText = data.text;
            uploadedText.value = data.text;
        } else {
            alert('Erreur: ' + (data.error || 'Impossible d\'extraire le texte'));
        }
    } catch (error) {
        alert('Erreur de connexion: ' + error.message);
    }
}

function clearFile() {
    selectedFile = null;
    currentText = '';
    uploadedText.value = '';
    fileInput.value = '';
    fileInfo.classList.add('hidden');
    dropZone.classList.remove('hidden');
}

function getCurrentText() {
    return (currentTab === 'upload') ? uploadedText.value : document.getElementById('pasteText').value;
}

function rewriteStyle() {
    const text = getCurrentText();
    if (!text) {
        alert('Veuillez entrer ou uploader un texte à réécrire');
        return;
    }
    
    const customStyle = document.getElementById('customStyle').value;
    const btn = document.getElementById('rewriteBtn');
    const resultCard = document.getElementById('resultCard');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const resultContent = document.getElementById('resultContent');
    
    btn.disabled = true;
    btn.innerHTML = '⏳ Traitement...';
    resultCard.classList.remove('hidden');
    loadingIndicator.classList.remove('hidden');
    resultContent.innerHTML = '';
    
    fetch(djangoApiUrl + '/api/style-rewrite/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            text: text,
            author_name: customStyle ? null : window.authorName,
            custom_style: customStyle
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingIndicator.classList.add('hidden');
        if (data.success && data.result) {
            resultContent.innerHTML = `
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 bg-red-50 p-4 rounded-xl">
                        <p class="text-xs font-semibold text-red-600 mb-2">📝 AVANT</p>
                        <div class="text-sm leading-relaxed max-h-96 overflow-y-auto">${escapeHtml(text)}</div>
                    </div>
                    <div class="flex-1 bg-green-50 p-4 rounded-xl">
                        <p class="text-xs font-semibold text-green-600 mb-2">✨ APRÈS RÉÉCRITURE</p>
                        <div class="text-sm leading-relaxed max-h-80 overflow-y-auto">${escapeHtml(data.result.rewritten_text || 'Non disponible')}</div>
                    </div>
                </div>
                ${data.result.style_characteristics_applied && data.result.style_characteristics_applied.length ? `
                <div class="mt-4 p-3 bg-blue-50 rounded-xl">
                    <p class="text-xs font-semibold text-blue-600 mb-2">🎨 Caractéristiques appliquées</p>
                    <div class="flex flex-wrap gap-2">
                        ${data.result.style_characteristics_applied.map(c => `<span class="text-xs px-2 py-1 rounded-full bg-white">${escapeHtml(c)}</span>`).join('')}
                    </div>
                </div>` : ''}
                ${data.result.explanation ? `<div class="mt-3 p-3 bg-gray-50 rounded-xl"><p class="text-xs text-gray-600">💡 ${escapeHtml(data.result.explanation)}</p></div>` : ''}
            `;
        } else {
            resultContent.innerHTML = `<p class="text-red-500 text-sm">Erreur: ${data.error || 'Erreur inconnue'}</p>`;
        }
    })
    .catch(error => {
        loadingIndicator.classList.add('hidden');
        resultContent.innerHTML = `<p class="text-red-500 text-sm">Erreur de connexion: ${error.message}</p>`;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '✨ Réécrire';
    });
}

function printResult() {
    const resultContent = document.getElementById('resultContent');
    const printWindow = window.open('', '_blank');
    const docContent = resultContent.innerHTML;
    const authorName = window.authorName;
    const currentDate = new Date().toLocaleString();
    const htmlContent = `
        <html>
        <head>
            <title>Rapport de réécriture - AGAU ÉDITIONS</title>
            <style>
                body { font-family: "Georgia", serif; padding: 40px; max-width: 800px; margin: 0 auto; }
                h1 { color: #0D3B64; font-family: "Lora", serif; }
                .original { background: #FEF2F2; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
                .rewritten { background: #F0FDF4; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
                .label { font-size: 12px; font-weight: bold; margin-bottom: 10px; }
                .footer { margin-top: 40px; padding-top: 20px; font-size: 10px; color: #94A3B8; text-align: center; border-top: 1px solid #E2E8F0; }
            </style>
        </head>
        <body>
            <h1>Rapport de réécriture stylistique</h1>
            <p>Auteur: ${authorName}</p>
            <p>Date: ${currentDate}</p>
            <hr>
            ${docContent}
            <div class="footer">Généré par AGAU ÉDITIONS - Assistant IA</div>
        </body>
        </html>
    `;
    printWindow.document.write(htmlContent);
    printWindow.document.close();
    printWindow.print();
}

// ═══════════════════════════════════════════════════════════════════════════
// 4. RELECTURE GRATUITE (APERÇU) – endpoints Laravel
// ═══════════════════════════════════════════════════════════════════════════

function switchReviewTab(tab) {
    const pasteDiv = document.getElementById('rl_paste');
    const uploadDiv = document.getElementById('rl_upload');
    const pasteBtn = document.getElementById('rlTabPasteBtn');
    const uploadBtn = document.getElementById('rlTabUploadBtn');
    
    if (tab === 'rl_paste') {
        pasteDiv.classList.remove('hidden');
        uploadDiv.classList.add('hidden');
        pasteBtn.style.cssText = 'background:#FEF9C3;color:#92400E;border-bottom:2px solid #EAB308;';
        uploadBtn.style.cssText = 'background:#F8FAFC;color:#94A3B8;border-bottom:none;';
    } else {
        pasteDiv.classList.add('hidden');
        uploadDiv.classList.remove('hidden');
        uploadBtn.style.cssText = 'background:#FEF9C3;color:#92400E;border-bottom:2px solid #EAB308;';
        pasteBtn.style.cssText = 'background:#F8FAFC;color:#94A3B8;border-bottom:none;';
    }
    currentReviewTab = tab;
}

// Upload fichier relecture
const rlDropZone = document.getElementById('rlDropZone');
const rlFileInput = document.getElementById('rlFileInput');
const rlFileInfo = document.getElementById('rlFileInfo');
const rlFileName = document.getElementById('rlFileName');
const reviewTextarea = document.getElementById('reviewText');

if (rlDropZone) {
    rlDropZone.addEventListener('click', () => rlFileInput.click());
    rlDropZone.addEventListener('dragover', (e) => { e.preventDefault(); rlDropZone.classList.add('drag-over'); });
    rlDropZone.addEventListener('dragleave', () => { rlDropZone.classList.remove('drag-over'); });
    rlDropZone.addEventListener('drop', async (e) => {
        e.preventDefault();
        rlDropZone.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) await processReviewFile(file);
    });
}
if (rlFileInput) {
    rlFileInput.addEventListener('change', async (e) => {
        if (e.target.files[0]) await processReviewFile(e.target.files[0]);
    });
}

async function processReviewFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    
    rlDropZone.classList.add('hidden');
    rlFileInfo.classList.remove('hidden');
    rlFileName.textContent = file.name;
    
    const response = await fetch(djangoApiUrl + '/api/extract-text/', {
        method: 'POST',
        body: formData
    });
    const data = await response.json();
    if (data.text) {
        reviewTextarea.value = data.text;
        const words = data.text.trim().split(/\s+/).filter(Boolean).length;
        document.getElementById('rlWordCount').textContent = `${words} mots`;
        switchReviewTab('rl_paste');
    } else {
        alert('Erreur extraction texte');
    }
}

function clearRlFile() {
    rlFileInput.value = '';
    rlFileInfo.classList.add('hidden');
    rlDropZone.classList.remove('hidden');
    reviewTextarea.value = '';
    document.getElementById('rlWordCount').textContent = '0 mot';
}

// Aperçu gratuit
async function launchFreeReview() {
    const text = reviewTextarea.value.trim();
    if (!text) {
        alert('Veuillez saisir ou uploader votre texte');
        return;
    }
    const btn = document.getElementById('freeReviewBtn');
    btn.disabled = true;
    btn.textContent = '⏳ Analyse...';
    
    try {
        const response = await fetch(djangoApiUrl + '/review/free/', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRFToken': window.csrfToken },
            body: JSON.stringify({ text, author_name: window.authorName })
        });
        const data = await response.json();
        if (data.success) {
            renderFreeResult(data.result);
        } else {
            alert('Erreur: ' + (data.error || 'Inconnue'));
        }
    } catch(e) {
        alert('Erreur réseau: ' + e.message);
    } finally {
        btn.disabled = false;
        btn.textContent = '👁️ Voir l\'aperçu gratuit';
    }
}

function renderFreeResult(r) {
    document.getElementById('freeWordCountBadge').textContent = `${r.stats?.words_analyzed || 0} / ${r.stats?.words_total || 0} mots`;
    let html = '';
    if (r.corrected_excerpt) {
        html += `<div class="mb-5">
            <h4 class="font-semibold text-sm mb-2" style="color:#0D3B64;">✅ Extrait corrigé (300 premiers mots)</h4>
            <div class="p-4 rounded-xl text-sm leading-relaxed whitespace-pre-wrap" style="background:#F0FDF4; border:1px solid #BBF7D0; color:#14532D;">${escapeHtml(r.corrected_excerpt)}</div>
        </div>`;
    }
    if (r.issues_found?.length) {
        html += `<div class="mb-5">
            <h4 class="font-semibold text-sm mb-2" style="color:#0D3B64;">⚠️ Problèmes détectés (${r.issues_found.length})</h4>
            <ul class="space-y-1.5">${r.issues_found.map(i => `<li class="flex items-start gap-2 text-sm" style="color:#475569;"><span class="text-orange-400 mt-0.5 flex-shrink-0">•</span>${escapeHtml(i)}</li>`).join('')}</ul>
        </div>`;
    }
    if (r.global_summary) {
        html += `<div class="p-4 rounded-xl" style="background:#FEF9C3; border:1px solid #FDE047;">
            <p class="text-sm font-medium mb-1" style="color:#713F12;">💡 Analyse globale</p>
            <p class="text-sm" style="color:#854D0E;">${escapeHtml(r.global_summary)}</p>
        </div>`;
    }
    if (r.stats?.is_truncated) {
        html += `<div class="mt-4 p-3 rounded-xl flex items-start gap-2" style="background:#EEF5FD; border:1px solid #BDDCF5;">
            <span style="color:#1D6FB8;">ℹ️</span>
            <p class="text-xs" style="color:#1D6FB8;">
                <strong>Aperçu limité à 300 mots</strong> sur ${r.stats.words_total} mots.
                La relecture complète couvre tout votre document pour seulement <strong>100 FCFA</strong>.
            </p>
        </div>`;
    }
    document.getElementById('freeResultContent').innerHTML = html;
    document.getElementById('freeResultCard').classList.remove('hidden');
    document.getElementById('freeResultCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ═══════════════════════════════════════════════════════════════════════════
// 5. PAIEMENT FEDAPAY (relecture complète) – endpoints Laravel
// ═══════════════════════════════════════════════════════════════════════════

async function launchPayment() {
    const text = reviewTextarea.value.trim();
    if (!text) {
        alert('Veuillez saisir ou uploader votre texte');
        return;
    }
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('modalContent').innerHTML = `<div class="text-center py-6"><div class="loading-spinner mx-auto mb-3"></div><p class="text-sm">Création de la transaction...</p></div>`;
    
    try {
        const response = await fetch(djangoApiUrl +'/review/payment/initiate/', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRFToken': window.csrfToken },
            body: JSON.stringify({
                text,
                author_name: window.authorName,
                format: document.querySelector('input[name="rlFormat"]:checked')?.value || 'pdf',
                email: document.getElementById('rlCustomerEmail').value.trim()
            })
        });
        const data = await response.json();
        if (data.success) {
            window.open(data.payment_url, '_blank');
            closePaymentModal();
            alert("Vous allez être redirigé après le paiement.");
        } else {
            document.getElementById('modalContent').innerHTML = `<div class="text-center py-4"><p class="text-red-500">❌ ${escapeHtml(data.error || 'Erreur')}</p><button onclick="closePaymentModal()" class="mt-4 px-4 py-2 rounded-xl" style="background:#EEF5FD;">Fermer</button></div>`;
        }
    } catch(e) {
        document.getElementById('modalContent').innerHTML = `<div class="text-center py-4"><p class="text-red-500">❌ Erreur réseau</p><button onclick="closePaymentModal()" class="mt-4 px-4 py-2 rounded-xl">Fermer</button></div>`;
    }
}

function startPolling() {
    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(pollPaymentStatus, 6000);
}

async function pollPaymentStatus() {
    if (!currentPaymentId) return;
    try {
        const resp = await fetch(djangoApiUrl +`/review/payment/${currentPaymentId}/status/`);
        const data = await resp.json();
        if (data.is_paid) {
            clearInterval(pollingInterval);
            closePaymentModal();
            showPaymentSuccess(data);
        }
    } catch(e) {}
}

async function checkPaymentManually() {
    if (!currentPaymentId) return;
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `<div class="text-center py-6"><div class="loading-spinner mx-auto mb-3"></div><p class="text-sm">Vérification...</p></div>`;
    try {
        const resp = await fetch(djangoApiUrl +`/review/payment/${currentPaymentId}/status/`);
        const data = await resp.json();
        if (data.is_paid) {
            closePaymentModal();
            showPaymentSuccess(data);
        } else {
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-sm mb-4">Statut : <strong>${data.status}</strong><br>Paiement non encore confirmé.</p>
                    <button onclick="checkPaymentManually()" class="px-4 py-2 rounded-xl text-sm font-medium mr-2" style="background:#1D6FB8; color:#fff;">Réessayer</button>
                    <button onclick="closePaymentModal()" class="px-4 py-2 rounded-xl text-sm border">Fermer</button>
                </div>`;
        }
    } catch(e) {}
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function showPaymentSuccess(data) {
    const card = document.getElementById('paymentResultCard');
    const content = document.getElementById('paymentResultContent');
    if (data.has_result) {
        content.innerHTML = `
            <div class="text-center py-6">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#22C55E" stroke-width="2"/></svg>
                </div>
                <h4 class="font-serif text-xl font-semibold mb-2" style="color:#0D3B64;">Relecture terminée ! 🎉</h4>
                <p class="text-sm mb-6">Votre document corrigé est prêt.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="${djangoApiUrl}/review/download/${data.payment_id}/?token=${data.token || ''}&format=pdf" class="px-6 py-3 rounded-xl font-semibold text-sm hover:opacity-90" style="background:#EF4444; color:#fff;" target="_blank">📄 Télécharger en PDF</a>
                    <a href="${djangoApiUrl}/review/download/${data.payment_id}/?token=${data.token || ''}&format=docx" class="px-6 py-3 rounded-xl font-semibold text-sm hover:opacity-90" style="background:#1D6FB8; color:#fff;" target="_blank">📝 Télécharger en Word</a>
                </div>
                <p class="text-xs text-gray-500 mt-6">✅ Le téléchargement a été lancé. Une fois le fichier récupéré, vous pouvez fermer cette page.</p>
            </div>`;
    } else {
        content.innerHTML = `<div class="text-center py-8"><div class="loading-spinner mx-auto mb-4" style="border-top-color:#EAB308;"></div><p class="text-sm">Paiement confirmé ! L'IA finalise la relecture…</p><p class="text-xs mt-1">Cette section se mettra à jour automatiquement.</p></div>`;
        pollingInterval = setInterval(async () => {
            try {
                const r = await fetch(djangoApiUrl +`/review/payment/${data.payment_id}/status/`);
                const d = await r.json();
                if (d.has_result) { clearInterval(pollingInterval); showPaymentSuccess(d); }
            } catch(e) {}
        }, 8000);
    }
    card.classList.remove('hidden');
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
// ═══════════════════════════════════════════════════════════════════════════
// 6. SUPPRESSION DE L'AUTEUR
// ═══════════════════════════════════════════════════════════════════════════

function deleteAuthor() {
    if (confirm('⚠️ Attention ! Supprimer cet auteur supprimera aussi tous ses manuscrits. Continuer ?')) {
        let authorId = null;
        const match = window.location.pathname.match(/\/auteurs\/(\d+)/);
        if (match) authorId = parseInt(match[1], 10);
        else if (typeof window.authorId === 'number') authorId = window.authorId;
        else { alert('Impossible de déterminer l\'auteur'); return; }
        
        fetch(djangoApiUrl + '/api/authors/' + authorId + '/', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) window.location.href = '/auteurs';
            else alert('Erreur: ' + (data.error || 'Impossible de supprimer'));
        })
        .catch(error => alert('Erreur: ' + error.message));
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// 7. UTILITAIRES
// ═══════════════════════════════════════════════════════════════════════════

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function openManuscriptModal() { document.getElementById('manuscriptModal').classList.remove('hidden'); }
function closeManuscriptModal() { document.getElementById('manuscriptModal').classList.add('hidden'); }
function openAuthorModal() {}  // non utilisée ici mais gardée pour compatibilité
function closeAuthorModal() {}
function saveAuthor() {}

// Initialisation du compteur de mots pour le textarea de relecture
document.addEventListener('DOMContentLoaded', () => {
    const reviewText = document.getElementById('reviewText');
    if (reviewText) {
        reviewText.addEventListener('input', function() {
            const words = this.value.trim().split(/\s+/).filter(Boolean).length;
            document.getElementById('rlWordCount').textContent = `${words} mot${words > 1 ? 's' : ''}`;
        });
    }
});