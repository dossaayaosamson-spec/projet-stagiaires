<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Models\ShortStoryPurchase;
use App\Models\User;
use App\Services\Payments\FedaPayPaymentService;
use App\Services\RoyaltyService;
use App\Services\SubscriptionService;
use FedaPay\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FedaPayWebhookController extends Controller
{
    public function __construct(
        private readonly FedaPayPaymentService $paymentService,
        private readonly SubscriptionService $subscriptionService
    ) {}

    /**
     * Gérer le webhook de FedaPay
     */
    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('dashboard')->with('info', 'Paiement annulé ou retour au site.');
        }

        $payload = $request->getContent();
        $sigHeader = $request->header('X-Fedapay-Signature');
        $secret = config('services.fedapay.webhook_secret');

        // Vérification de la signature si le secret est configuré
        if ($secret) {
            try {
                Webhook::constructEvent($payload, $sigHeader, $secret);
            } catch (\Exception $e) {
                Log::error('Signature Webhook FedaPay invalide: '.$e->getMessage());

                return response()->json(['error' => 'Invalid signature'], 400);
            }
        } else {
            Log::warning('Webhook FedaPay reçu sans secret de signature configuré. Sécurité affaiblie.');
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? null;

        Log::info('FedaPay Webhook reçu', ['event' => $event]);

        $transactionData = $data['entity'] ?? [];
        $transactionId = $transactionData['id'] ?? null;

        match ($event) {
            'transaction.approved' => $this->processApproved($transactionId),
            'transaction.declined' => $this->processDeclinedOrCanceled($transactionId, 'déclinée'),
            'transaction.canceled' => $this->processDeclinedOrCanceled($transactionId, 'annulée'),
            'transaction.transferred' => Log::info("Webhook FedaPay: Fonds transférés pour transaction #{$transactionId}"),
            'transaction.updated' => Log::info("Webhook FedaPay: Transaction #{$transactionId} mise à jour"),
            'transaction.created' => Log::info("Webhook FedaPay: Transaction #{$transactionId} créée"),
            default => Log::info("Webhook FedaPay: Événement non géré '{$event}'"),
        };

        return response()->json(['status' => 'success']);
    }

    /**
     * Extraire les métadonnées d'une transaction FedaPay (objet ou tableau).
     */
    private function extractMetadata(mixed $transaction): array
    {
        $metadata = $transaction->metadata;
        if (is_object($metadata)) {
            return [
                'type' => $metadata->type ?? null,
                'order_id' => $metadata->order_id ?? null,
                'user_id' => $metadata->user_id ?? null,
                'plan_name' => $metadata->plan_name ?? null,
                'short_story_id' => $metadata->short_story_id ?? null,
                'idempotency_key' => $metadata->idempotency_key ?? null,
            ];
        }
        if (is_array($metadata)) {
            return [
                'type' => $metadata['type'] ?? null,
                'order_id' => $metadata['order_id'] ?? null,
                'user_id' => $metadata['user_id'] ?? null,
                'plan_name' => $metadata['plan_name'] ?? null,
                'short_story_id' => $metadata['short_story_id'] ?? null,
                'idempotency_key' => $metadata['idempotency_key'] ?? null,
            ];
        }

        return ['type' => null, 'order_id' => null, 'user_id' => null, 'plan_name' => null, 'short_story_id' => null, 'idempotency_key' => null];
    }

    /**
     * Traiter un paiement approuvé.
     */
    private function processApproved(?string $transactionId): void
    {
        if (! $transactionId) {
            Log::warning('Webhook FedaPay: transaction.approved reçu sans ID de transaction');

            return;
        }

        try {
            $transaction = $this->paymentService->retrieveTransaction($transactionId);
            $meta = $this->extractMetadata($transaction);

            Log::info('Webhook FedaPay: Métadonnées extraites', $meta);

            if ($meta['type'] === 'order' && $meta['order_id']) {
                $this->handleOrderPayment($meta['order_id']);
            } elseif ($meta['type'] === 'subscription' && $meta['user_id'] && $meta['plan_name']) {
                $this->handleSubscriptionPayment($meta['user_id'], $meta['plan_name']);
            } elseif ($meta['type'] === 'short_story' && $meta['user_id'] && $meta['short_story_id'] && $meta['idempotency_key']) {
                $this->handleShortStoryPayment($meta['user_id'], $meta['short_story_id'], $meta['idempotency_key'], $transactionId);
            } else {
                Log::warning('Webhook FedaPay: Métadonnées incomplètes ou type inconnu', $meta);
            }
        } catch (\Exception $e) {
            Log::error('Erreur traitement webhook FedaPay (approved): '.$e->getMessage(), [
                'transaction_id' => $transactionId,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Traiter un paiement décliné ou annulé — marque la commande comme annulée.
     */
    private function processDeclinedOrCanceled(?string $transactionId, string $reason): void
    {
        if (! $transactionId) {
            Log::warning("Webhook FedaPay: {$reason} reçu sans ID de transaction");

            return;
        }

        try {
            $transaction = $this->paymentService->retrieveTransaction($transactionId);
            $meta = $this->extractMetadata($transaction);

            Log::info("Webhook FedaPay: Transaction {$reason}", ['transaction_id' => $transactionId, 'meta' => $meta]);

            if ($meta['type'] === 'order' && $meta['order_id']) {
                $order = Order::find($meta['order_id']);
                if ($order && $order->statut === 'en_attente') {
                    $order->statut = 'annulée';
                    $order->save();
                    Log::info("Commande #{$meta['order_id']} marquée 'annulée' (raison: {$reason}) via Webhook");
                }
            } elseif ($meta['type'] === 'short_story' && $meta['idempotency_key']) {
                $purchase = ShortStoryPurchase::where('idempotency_key', $meta['idempotency_key'])->first();
                if ($purchase && $purchase->status === 'pending') {
                    $purchase->status = 'failed';
                    $purchase->save();
                    Log::info("Achat Short Story marqué 'failed' (raison: {$reason}) via Webhook");
                }
            }
        } catch (\Exception $e) {
            Log::error("Erreur traitement webhook FedaPay ({$reason}): ".$e->getMessage(), [
                'transaction_id' => $transactionId,
            ]);
        }
    }

    private function handleOrderPayment($orderId)
    {
        Log::info("Webhook: Recherche de la commande #{$orderId}");
        $order = Order::find($orderId);

        if ($order) {
            Log::info("Webhook: Commande #{$orderId} trouvée. Statut actuel: {$order->statut}");
            if ($order->statut === 'en_attente') {
                $order->statut = 'livrée';
                $order->save();

                app(RoyaltyService::class)->processOrderRoyalties($order);

                try {
                    Mail::to($order->user->email)->send(new OrderConfirmed($order));
                } catch (\Exception $e) {
                    Log::error('Erreur email confirmation commande (Webhook): '.$e->getMessage());
                }

                Log::info("Commande #{$orderId} marquée comme LIVRÉE via Webhook");
            } else {
                Log::info("Webhook: Commande #{$orderId} déjà traitée (Statut: {$order->statut})");
            }
        } else {
            Log::error("Webhook: Commande #{$orderId} non trouvée dans la base de données");
        }
    }

    private function handleSubscriptionPayment($userId, $planName)
    {
        Log::info("Webhook: Recherche de l'utilisateur #{$userId} pour abonnement {$planName}");
        $user = User::find($userId);
        if ($user) {
            $plans = collect(config('plans'));
            $selectedPlan = $plans->firstWhere('nom', $planName);

            if ($selectedPlan) {
                $this->subscriptionService->activateSubscription($user, $selectedPlan);
                Log::info("Abonnement {$planName} activé pour l'utilisateur #{$userId} via Webhook");
            } else {
                Log::error("Webhook: Plan '{$planName}' non trouvé dans la configuration");
            }
        } else {
            Log::error("Webhook: Utilisateur #{$userId} non trouvé pour l'abonnement");
        }
    }

    private function handleShortStoryPayment($userId, $shortStoryId, $idempotencyKey, $transactionId)
    {
        Log::info("Webhook: Traitement du paiement Short Story pour l'utilisateur #{$userId}, story #{$shortStoryId}");

        $purchase = ShortStoryPurchase::where('idempotency_key', $idempotencyKey)->first();

        if ($purchase) {
            if ($purchase->status === 'pending') {
                $purchase->status = 'successful';
                $purchase->transaction_reference = $transactionId;
                $purchase->save();
                Log::info("Achat Short Story #{$shortStoryId} validé pour l'utilisateur #{$userId} via Webhook");
            } else {
                Log::info("Webhook: Achat Short Story déjà traité (Statut: {$purchase->status})");
            }
        } else {
            Log::error("Webhook: Achat Short Story avec idempotency_key {$idempotencyKey} non trouvé");
        }
    }
}
