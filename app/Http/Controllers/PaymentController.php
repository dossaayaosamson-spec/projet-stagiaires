<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Services\Payments\FedaPayPaymentService;
use App\Services\RoyaltyService;
use App\Services\SubscriptionService;
use FedaPay\Error\ApiConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function __construct(
        private readonly FedaPayPaymentService $paymentService,
        private readonly SubscriptionService $subscriptionService
    ) {}

    /**
     * Initialiser et lancer le paiement
     */
    public function pay(Order $order)
    {
        // Vérification que l'utilisateur est propriétaire de la commande
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Accès refusé.');
        }

        // Vérification du statut de la commande
        if ($order->statut !== 'en_attente') {
            return redirect()->route('client.orders')->with('warning', 'Cette commande a déjà été traitée.');
        }

        // Vérification que la clé API est configurée
        if (! $this->paymentService->isConfigured()) {
            return back()->with('error', 'Configuration de paiement manquante. Veuillez contacter l\'administrateur.');
        }

        try {
            $order->payment_method = 'fedapay';
            $order->save();

            $redirectUrl = route('payment.return', ['order' => $order->id], true);

            // Création de la transaction chez FedaPay
            $transaction = $this->paymentService->createTransaction($order, $redirectUrl);

            // Génération du token de paiement
            $token = $transaction->generateToken();

            // Vider le panier seulement après avoir généré le token avec succès
            session()->forget('cart');

            // Redirection vers la page de paiement FedaPay
            return redirect($token->url);

        } catch (ApiConnection $e) {
            Log::error('FedaPay Connection Error: '.$e->getMessage(), ['order_id' => $order->id]);

            return back()->with('error', 'Erreur de connexion à la plateforme de paiement. Veuillez réessayer plus tard.');
        } catch (\Exception $e) {
            Log::error('FedaPay General Error: '.$e->getMessage(), [
                'order_id' => $order->id,
                'exception' => get_class($e),
                'details' => method_exists($e, 'getJsonBody') ? $e->getJsonBody() : null,
            ]);

            return back()->with('error', 'Une erreur est survenue lors du paiement. Veuillez réessayer.');
        }
    }

    /**
     * Retour après paiement (redirection depuis FedaPay)
     */
    public function retour(Request $request, Order $order)
    {
        // Vérification que l'utilisateur est propriétaire de la commande
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Accès refusé.');
        }

        // FedaPay retourne ?id=XXX&status=YYY dans l'URL de retour (doc officielle)
        $transactionId = $request->input('id') ?? $request->input('transaction_id');

        if (empty($transactionId)) {
            return redirect()->route('client.orders')->with('info', 'Retour de paiement sans identifiant. Vérifiez le statut de votre commande.');
        }

        try {
            $transaction = $this->paymentService->retrieveTransaction($transactionId);

            // En sandbox, FedaPay retourne "pending" pour les paiements simulés — on les considère comme approuvés
            $isApproved = $transaction->status === 'approved'
                || ($transaction->status === 'pending' && $this->paymentService->isSandbox());

            if ($isApproved) {
                $order->statut = 'livrée';
                $order->save();

                app(RoyaltyService::class)->processOrderRoyalties($order);

                // Vider le panier
                session()->forget('cart');

                // Email de confirmation (le webhook le fait aussi, mais on garde ici par sécurité)
                try {
                    Mail::to($order->user->email)->send(new OrderConfirmed($order));
                } catch (\Exception $e) {
                    Log::error('Erreur envoi email (Retour): '.$e->getMessage());
                }

                return redirect()->route('payment.success', ['order' => $order->id]);
            }

            return redirect()->route('client.orders')
                ->with('error', 'Le paiement a été annulé ou refusé. Statut : '.$transaction->status);
        } catch (\Exception $e) {
            return redirect()->route('client.orders')
                ->with('warning', 'Retour de paiement reçu. Votre commande sera traitée dès confirmation.');
        }
    }

    /**
     * Retour après paiement d'un abonnement
     */
    public function subscriptionReturn(Request $request, string $plan)
    {
        // FedaPay retourne ?id=XXX&status=YYY dans l'URL de retour (doc officielle)
        $transactionId = $request->input('id') ?? $request->input('transaction_id');

        if (empty($transactionId)) {
            return redirect()->route('client.subscription')->with('info', 'Retour de paiement. Vérifiez votre statut d\'abonnement.');
        }

        try {
            $transaction = $this->paymentService->retrieveTransaction($transactionId);

            if ($transaction->status === 'approved') {
                // Activer l'abonnement
                $plans = collect(config('plans'));
                $selectedPlan = $plans->firstWhere('nom', $plan);

                if ($selectedPlan) {
                    $this->subscriptionService->activateSubscription(Auth::user(), $selectedPlan);

                    return redirect()->route('client.subscription')->with('success', 'Votre abonnement '.$plan.' a été activé avec succès !');
                }
            }

            return redirect()->route('client.subscription')->with('warning', 'Paiement en attente ou annulé. Statut : '.$transaction->status);
        } catch (\Exception $e) {
            return redirect()->route('client.subscription')->with('warning', 'Retour de paiement reçu. Votre abonnement sera activé dès confirmation.');
        }
    }

    /**
     * Page de succès après paiement
     */
    public function success(Order $order)
    {
        // Vérification que l'utilisateur est propriétaire de la commande
        if (Auth::id() !== $order->user_id) {
            abort(403);
        }

        // Vérification que la commande est bien payée
        if (! in_array($order->statut, ['confirmée', 'livrée'])) {
            return redirect()->route('client.orders');
        }

        return view('front.payment-success', compact('order'));
    }
}
