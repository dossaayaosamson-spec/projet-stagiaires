<?php

namespace App\Services\Payments;

use App\Models\Order;
use FedaPay\FedaPay;
use FedaPay\Transaction;
use Illuminate\Support\Facades\Log;

class FedaPayPaymentService
{
    public function isConfigured(): bool
    {
        return ! empty(config('services.fedapay.api_key'));
    }

    public function isSandbox(): bool
    {
        return config('services.fedapay.environment', 'sandbox') === 'sandbox';
    }

    public function createTransaction(Order $order, string $redirectUrl): mixed
    {
        $this->configureClient();

        $customer = [
            'firstname' => $order->user->prenom ?? 'Client',
            'lastname' => $order->user->nom_famille ?? '',
            'email' => $order->user->email,
        ];

        if (! empty($order->user->telephone)) {
            $customer['phone_number'] = [
                'number' => $order->user->telephone,
                'country' => 'tg',
            ];
        }

        return Transaction::create([
            'amount' => (int) round($order->total),
            'currency' => ['iso' => 'XOF'],
            'description' => "Paiement commande #{$order->id}",
            'customer' => $customer,
            'callback_url' => $redirectUrl,
            'cancel_url' => $redirectUrl,
            'metadata' => [
                'type' => 'order',
                'order_id' => $order->id,
            ],
        ]);
    }

    public function createSubscriptionTransaction($user, $plan, $amount, string $redirectUrl): mixed
    {
        $this->configureClient();

        $customer = [
            'firstname' => $user->prenom ?? 'Client',
            'lastname' => $user->nom_famille ?? '',
            'email' => $user->email,
        ];

        if (! empty($user->telephone)) {
            $customer['phone_number'] = [
                'number' => $user->telephone,
                'country' => 'tg',
            ];
        }

        return Transaction::create([
            'amount' => (int) round($amount),
            'currency' => ['iso' => 'XOF'],
            'description' => "Abonnement {$plan} - {$user->prenom} {$user->nom_famille}",
            'customer' => $customer,
            'callback_url' => $redirectUrl,
            'cancel_url' => $redirectUrl,
            'metadata' => [
                'type' => 'subscription',
                'user_id' => $user->id,
                'plan_name' => $plan,
            ],
        ]);
    }

    public function createShortStoryTransaction($user, $story, $amount, string $redirectUrl, string $idempotencyKey): mixed
    {
        $this->configureClient();

        $customer = [
            'firstname' => $user->prenom ?? 'Client',
            'lastname' => $user->nom_famille ?? '',
            'email' => $user->email,
        ];

        if (! empty($user->telephone)) {
            $customer['phone_number'] = [
                'number' => $user->telephone,
                'country' => 'tg',
            ];
        }

        return Transaction::create([
            'amount' => (int) round($amount),
            'currency' => ['iso' => 'XOF'],
            'description' => "Achat Short Story - {$story->titre}",
            'customer' => $customer,
            'callback_url' => $redirectUrl,
            'cancel_url' => $redirectUrl,
            'metadata' => [
                'type' => 'short_story',
                'user_id' => $user->id,
                'short_story_id' => $story->id,
                'idempotency_key' => $idempotencyKey,
            ],
        ]);
    }

    public function retrieveTransaction(string $transactionId): mixed
    {
        $this->configureClient();

        return Transaction::retrieve($transactionId);
    }

    private function configureClient(): void
    {
        $apiKey = config('services.fedapay.api_key');
        $environment = config('services.fedapay.environment', 'sandbox');

        FedaPay::setApiKey($apiKey);
        FedaPay::setEnvironment($environment);

        if (app()->environment('local')) {
            FedaPay::setVerifySslCerts(false);
            Log::debug('FedaPay: SSL Verification disabled (local environment)');
        }
    }
}
