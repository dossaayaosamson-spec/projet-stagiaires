<?php

require 'vendor/autoload.php';

use FedaPay\FedaPay;
use FedaPay\Transaction;

// Load .env manually for the script if needed, or just use values
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$key = $_ENV['FEDAPAY_API_KEY'] ?? '';
$env = $_ENV['FEDAPAY_ENVIRONMENT'] ?? 'sandbox';

echo 'Testing FedaPay with Key: '.substr($key, 0, 10)."...\n";
echo "Environment: $env\n";

if (empty($key)) {
    echo "ERROR: FEDAPAY_API_KEY is empty!\n";
    exit(1);
}

FedaPay::setApiKey($key);
FedaPay::setEnvironment($env);

try {
    echo "Attempting to list transactions (simple API call)...\n";
    $transactions = Transaction::all(['per_page' => 1]);
    echo 'SUCCESS: Connection established. Found '.count($transactions)." transactions.\n";
} catch (Exception $e) {
    echo 'FAILURE: '.$e->getMessage()."\n";
}
