<?php

use Illuminate\Contracts\Console\Kernel;

// clear_cache.php - À mettre à la racine du serveur
// Accédez via le navigateur : https://agaueditions.saeicubetech.com/clear_cache.php

// Vérification de sécurité basique
if (! isset($_GET['key']) || $_GET['key'] !== 'clear_cache_'.md5('agau_editions')) {
    exit('Accès refusé.');
}

require __DIR__.'/bootstrap/app.php';

try {
    $kernel = app(Kernel::class);

    echo '<h1>Nettoyage des caches...</h1>';
    echo '<pre>';

    // Vider tous les caches
    echo '1. Route cache: ';
    $kernel->call('route:clear');
    echo "✓ OK\n";

    echo '2. Config cache: ';
    $kernel->call('config:clear');
    echo "✓ OK\n";

    echo '3. View cache: ';
    $kernel->call('view:clear');
    echo "✓ OK\n";

    echo "\n✅ TOUS LES CACHES ONT ÉTÉ VIDÉS\n";
    echo "Les routes sont maintenant refreshées !\n";
    echo '</pre>';

} catch (Exception $e) {
    echo '<h1>❌ ERREUR</h1>';
    echo '<pre>'.$e->getMessage().'</pre>';
}
