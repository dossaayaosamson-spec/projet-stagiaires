<?php

// Augmenter le temps maximum d'exécution (npm install et npm run build peuvent être longs)
set_time_limit(300);

$envPath = __DIR__.'/../.env';
$envToken = null;

// Lecture simple du .env (sans lancer Laravel) pour récupérer le DEPLOY_TOKEN
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#')) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            [$name, $value] = explode('=', $line, 2);
            if (trim($name) === 'DEPLOY_TOKEN') {
                $envToken = trim($value, '"\' ');
                break;
            }
        }
    }
}

// Récupération du jeton depuis l'URL (?token=XYZ)
$providedToken = $_GET['token'] ?? '';

// Vérification de sécurité
if (! $envToken || ! hash_equals($envToken, $providedToken)) {
    http_response_code(403);
    exit('Accès non autorisé.');
}

// Se placer à la racine du projet
chdir(__DIR__.'/../');

// Liste des commandes de déploiement
$commands = [
    'echo "=== Début du déploiement ==="',
    'git pull origin main 2>&1',
    'npm install 2>&1',
    'npm run build 2>&1',
    'composer install --no-interaction --prefer-dist --optimize-autoloader 2>&1',
    'php artisan optimize:clear 2>&1',
    'php artisan optimize 2>&1',
    'php artisan migrate --force 2>&1',
    'echo "=== Déploiement terminé avec succès ==="',
];

$output = '';

// Exécution des commandes
foreach ($commands as $command) {
    $output .= "$ {$command}\n";
    $result = shell_exec($command);
    $output .= $result."\n\n";
}

// Retourner le résultat en texte brut pour que GitHub Actions puisse le lire
header('Content-Type: text/plain');
echo $output;
