<?php

require 'vendor/autoload.php';

use FedaPay\FedaPay;
use FedaPay\Transaction;

// Load .env manually
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

$key = $_ENV['FEDAPAY_API_KEY'] ?? '';
$env = $_ENV['FEDAPAY_ENVIRONMENT'] ?? 'sandbox';

echo "Testing FedaPay Transaction Creation...\n";
echo 'Key: '.substr($key, 0, 10)."...\n";
echo "Environment: $env\n";

FedaPay::setApiKey($key);
FedaPay::setEnvironment($env);
FedaPay::setVerifySslCerts(false);

try {
    echo "Creating a test transaction of 500 XOF...\n";
    $transaction = Transaction::create([
        'amount' => 500,
        'currency' => ['iso' => 'XOF'],
        'description' => 'Test transaction from CLI',
        'customer' => [
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com',
        ],
        'callback_url' => 'https://example.com/callback',
        'redirect_url' => 'https://example.com/redirect',
    ]);

    echo "SUCCESS! Transaction created.\n";
    echo 'ID: '.$transaction->id."\n";

    $token = $transaction->generateToken();
    echo 'Payment URL: '.$token->url."\n";
} catch (Exception $e) {
    echo 'FAILURE: '.$e->getMessage()."\n";
    if (method_exists($e, 'getJsonBody')) {
        print_r($e->getJsonBody());
    }
}
