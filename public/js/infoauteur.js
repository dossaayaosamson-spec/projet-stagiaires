// Variables
let currentText = '';
let selectedFile = null;
let manuscriptFile = null;
let manuscriptFileContent = '';
const djangoApiUrl = 'https://agaueditions-ia.saeicubetech.com';


// Onglets manuscrit modal
function switchManuscriptTab(tab) {
    const uploadDiv = document.getElementById('msTabUpload');
    const pasteDiv = document.getElementById('msTabPaste');
    const uploadBtn = document.getElementById('msTabUploadBtn');
    const pasteBtn = document.getElementById('msTabPasteBtn');
    
    if (tab === 'ms_upload') {
        uploadDiv.classList.remove('hidden');
        pasteDiv.classList.add('hidden');
        uploadBtn.style.background = '#EEF5FD';
        uploadBtn.style.color = '#1D6FB8';
        uploadBtn.style.borderBottom = '2px solid #1D6FB8';
        pasteBtn.style.background = '#F8FAFC';
        pasteBtn.style.color = '#94A3B8';
        pasteBtn.style.borderBottom = 'none';
    } else {
        uploadDiv.classList.add('hidden');
        pasteDiv.classList.remove('hidden');
        pasteBtn.style.background = '#EEF5FD';
        pasteBtn.style.color = '#1D6FB8';
        pasteBtn.style.borderBottom = '2px solid #1D6FB8';
        uploadBtn.style.background = '#F8FAFC';
        uploadBtn.style.color = '#94A3B8';
        uploadBtn.style.borderBottom = 'none';
    }
}

// Manuscript file upload
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

// Main assistant functions
function switchTab(tab) {
    const uploadTab = document.getElementById('tabUpload');
    const pasteTab = document.getElementById('tabPaste');
    const uploadBtn = document.getElementById('tabUploadBtn');
    const pasteBtn = document.getElementById('tabPasteBtn');
    
    if (tab === 'upload') {
        uploadTab.classList.remove('hidden');
        pasteTab.classList.add('hidden');
        uploadBtn.style.background = '#EEF5FD';
        uploadBtn.style.color = '#1D6FB8';
        uploadBtn.style.borderBottom = '2px solid #1D6FB8';
        pasteBtn.style.background = '#F8FAFC';
        pasteBtn.style.color = '#94A3B8';
        pasteBtn.style.borderBottom = 'none';
    } else {
        uploadTab.classList.add('hidden');
        pasteTab.classList.remove('hidden');
        pasteBtn.style.background = '#EEF5FD';
        pasteBtn.style.color = '#1D6FB8';
        pasteBtn.style.borderBottom = '2px solid #1D6FB8';
        uploadBtn.style.background = '#F8FAFC';
        uploadBtn.style.color = '#94A3B8';
        uploadBtn.style.borderBottom = 'none';
    }
}

// File upload handling for main assistant
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
    const uploadTab = document.getElementById('tabUpload');
    const activeTab = uploadTab && !uploadTab.classList.contains('hidden') ? 'upload' : 'paste';
    if (activeTab === 'upload') {
        return uploadedText.value;
    } else {
        return document.getElementById('pasteText').value;
    }
}

// Add Manuscript with auto-indexing
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
        console.log('Paste mode - content length:', content ? content.length : 0);
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
            author_id: parseInt(window.authorId, 10),
            title: formData.get('title'),
            content: content
        })
    })
    .then(response => {
    if (!response.ok) {
        return response.text().then(text => {
            console.error('Erreur serveur:', text);  // ← voir la vraie erreur dans la console
            throw new Error(text);
        });
    }
    return response.json();
})
    .then(data => {
        if (data.success) {
            return fetch(djangoApiUrl + '/api/index-manuscript/' + data.manuscript_id + '/', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }
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
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de l\'indexation');
        }
    });
}

function trainAuthor() {
    const btn = document.getElementById('trainBtn');
    if (!btn) return;

    // Extraire l'ID de l'auteur depuis l'URL (ex: /auteurs/2)
    let authorId = null;
    const match = window.location.pathname.match(/\/auteurs\/(\d+)/);
    if (match) {
        authorId = parseInt(match[1], 10);
    } else if (typeof window.authorId === 'number') {
        authorId = window.authorId;
    } else {
        alert('Impossible de déterminer l\'auteur');
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
        alert('Erreur: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '🧠 Entraîner l\'IA';
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
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + (data.error || 'Impossible de supprimer'));
            }
        })
        .catch(error => { alert('Erreur: ' + error.message); });
    }
}

function deleteAuthor() {
    if (confirm('⚠️ Attention ! Supprimer cet auteur supprimera aussi tous ses manuscrits. Continuer ?')) {
        // Extraire l'ID de l'auteur depuis l'URL
        let authorId = null;
        const match = window.location.pathname.match(/\/auteurs\/(\d+)/);
        if (match) {
            authorId = parseInt(match[1], 10);
        } else if (typeof window.authorId === 'number') {
            authorId = window.authorId;
        } else {
            alert('Impossible de déterminer l\'auteur à supprimer.');
            return;
        }

        fetch(djangoApiUrl + '/api/authors/' + authorId + '/', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/auteurs';
            } else {
                alert('Erreur: ' + (data.error || 'Impossible de supprimer'));
            }
        })
        .catch(error => alert('Erreur: ' + error.message));
    }
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

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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

function openManuscriptModal() { document.getElementById('manuscriptModal').classList.remove('hidden'); }
function closeManuscriptModal() { document.getElementById('manuscriptModal').classList.add('hidden'); }