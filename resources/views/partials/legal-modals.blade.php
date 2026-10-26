<!-- Modal : CGU -->
<div id="modal-cgu" class="fixed inset-0 z-[150] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('modal-cgu')"></div>
    <div class="relative bg-white w-full max-w-2xl max-h-[85vh] overflow-y-auto rounded-2xl shadow-2xl animate-in fade-in zoom-in duration-300">
        <div class="sticky top-0 bg-white border-b border-agau-border/30 px-8 py-5 flex items-center justify-between">
            <h3 class="font-serif text-xl font-bold text-agau-text m-0">Conditions d'Utilisation - AGAU EDITIONS</h3>
            <button onclick="closeModal('modal-cgu')" class="bg-transparent border-none text-agau-muted hover:text-agau-blue cursor-pointer transition-colors p-2 flex items-center justify-center rounded-full hover:bg-agau-blue/5">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-8 space-y-8">
            <div class="space-y-3">
                <h4 class="text-[10px] font-black text-agau-blue uppercase tracking-widest">1. Objet de l'abonnement</h4>
                <p class="text-[14px] text-agau-muted leading-relaxed">
                    L'abonnement donne accès à notre catalogue numérique de livres en illimité selon la formule choisie. L'accès est personnel et non transmissible.
                </p>
            </div>
            <div class="space-y-3">
                <h4 class="text-[10px] font-black text-agau-blue uppercase tracking-widest">2. Paiement et Renouvellement</h4>
                <p class="text-[14px] text-agau-muted leading-relaxed">
                    Les différents types d'abonnement que nous offrons sont dans la section "offres". Vous pouvez faire le choix selon votre convenance. Le paiement est effectué par mobile money.
                </p>
            </div>
            <div class="space-y-3">
                <h4 class="text-[10px] font-black text-agau-blue uppercase tracking-widest">3. Usage des contenus</h4>
                <p class="text-[14px] text-agau-muted leading-relaxed">
                    Les livres sont protégés par le droit d'auteur. Le téléchargement n'est pas autorisé. Toute reproduction est interdite.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal : Politique de Confidentialité -->
<div id="modal-privacy" class="fixed inset-0 z-[150] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('modal-privacy')"></div>
    <div class="relative bg-white w-full max-w-2xl max-h-[85vh] overflow-y-auto rounded-2xl shadow-2xl animate-in fade-in zoom-in duration-300">
        <div class="sticky top-0 bg-white border-b border-agau-border/30 px-8 py-5 flex items-center justify-between">
            <h3 class="font-serif text-xl font-bold text-agau-text m-0">Politique de Confidentialité - AGAU EDITIONS</h3>
            <button onclick="closeModal('modal-privacy')" class="bg-transparent border-none text-agau-muted hover:text-agau-blue cursor-pointer transition-colors p-2 flex items-center justify-center rounded-full hover:bg-agau-blue/5">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-8 space-y-8">
            <div class="space-y-3">
                <h4 class="text-[10px] font-black text-agau-blue uppercase tracking-widest">1. Données collectées</h4>
                <p class="text-[14px] text-agau-muted leading-relaxed">
                    Nous collectons votre nom, email et préférences de lecture pour améliorer vos recommandations de livres.
                </p>
            </div>
            <div class="space-y-3">
                <h4 class="text-[10px] font-black text-agau-blue uppercase tracking-widest">2. Utilisation des données</h4>
                <p class="text-[14px] text-agau-muted leading-relaxed">
                    Vos données de paiement sont sécurisées par notre partenaire de paiement (Fedapay) et ne sont jamais stockées sur nos serveurs.
                </p>
            </div>
            <div class="space-y-3">
                <h4 class="text-[10px] font-black text-agau-blue uppercase tracking-widest">3. Cookies</h4>
                <p class="text-[14px] text-agau-muted leading-relaxed">
                    Nous utilisons des cookies pour maintenir votre session ouverte et mémoriser votre progression dans vos lectures en cours.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Close on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeModal('modal-cgu');
        closeModal('modal-privacy');
    }
});
</script>
