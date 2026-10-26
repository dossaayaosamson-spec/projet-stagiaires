<?php

namespace App\Http\Controllers;

use App\Models\ShortStory;
use App\Models\ShortStoryPurchase;
use App\Services\Payments\FedaPayPaymentService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShortStoryPaymentController extends Controller
{
    public function __construct(private readonly FedaPayPaymentService $paymentService)
    {
        // Le middleware 'auth' est déjà appliqué dans routes/web.php
    }

    public function pay(Request $request, ShortStory $story)
    {
        $user = Auth::user();

        // Check if user already has access
        if ($story->is_free || $user->role === 'admin' || ($user->author && $story->author_id === $user->author->id) || app(SubscriptionService::class)->hasActiveSubscription($user)) {
            return redirect()->route('front.stories.show', $story->frontRouteParams())->with('info', 'Vous avez déjà accès à cette histoire.');
        }

        // Check if user has already purchased
        $hasPurchased = ShortStoryPurchase::where('user_id', $user->id)
            ->where('short_story_id', $story->id)
            ->where('status', 'successful')
            ->exists();

        if ($hasPurchased) {
            return redirect()->route('front.stories.show', $story->frontRouteParams())->with('info', 'Vous avez déjà acheté cette histoire.');
        }

        // Price is fixed at 500 FCFA
        $amount = 500;

        $idempotencyKey = Str::uuid()->toString();

        // Create pending purchase
        $purchase = ShortStoryPurchase::create([
            'user_id' => $user->id,
            'short_story_id' => $story->id,
            'amount' => $amount,
            'status' => 'pending',
            'idempotency_key' => $idempotencyKey,
        ]);

        $redirectUrl = route('front.stories.payment.callback', ['purchase' => $purchase->id]);

        try {
            $transaction = $this->paymentService->createShortStoryTransaction(
                $user,
                $story,
                $amount,
                $redirectUrl,
                $idempotencyKey
            );

            return redirect($transaction->generateToken()->url);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'initialisation du paiement FedaPay pour Short Story: '.$e->getMessage());

            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'initialisation du paiement.');
        }
    }

    public function callback(Request $request, ShortStoryPurchase $purchase)
    {
        // FedaPay sends 'id' parameter in callback for transaction id
        $transactionId = $request->query('id');
        $status = $request->query('status'); // can be approved, canceled, declined

        if ($status === 'approved') {
            // Check if webhook already updated it
            if ($purchase->status === 'pending') {
                try {
                    $transaction = $this->paymentService->retrieveTransaction($transactionId);

                    if ($transaction->status === 'approved') {
                        $purchase->status = 'successful';
                        $purchase->transaction_reference = $transactionId;
                        $purchase->save();
                    }
                } catch (\Exception $e) {
                    \Log::error('Erreur de validation manuelle du paiement FedaPay: '.$e->getMessage());
                }
            }

            if ($purchase->status === 'successful') {
                return redirect()->route('front.stories.show', $purchase->shortStory->frontRouteParams())
                    ->with('success', 'Paiement effectué avec succès. Bonne lecture !');
            }
        }

        // If canceled or declined
        if ($purchase->status === 'pending') {
            $purchase->status = 'failed';
            $purchase->save();
        }

        return redirect()->route('front.stories.show', $purchase->shortStory->frontRouteParams())
            ->with('error', 'Le paiement a été annulé ou a échoué.');
    }
}
