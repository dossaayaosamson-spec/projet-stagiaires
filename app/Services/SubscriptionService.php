<?php

namespace App\Services;

use App\Mail\SubscriptionConfirmed;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SubscriptionService
{
    /**
     * Activer l'abonnement après paiement
     */
    public function activateSubscription(User $user, array $selectedPlan): void
    {
        $durations = [
            'Hebdomadaire' => 7,
            'Mensuel' => 30,
            'Trimestriel' => 90,
            'Semestriel' => 180,
            'Annuel' => 365,
        ];

        $days = $durations[$selectedPlan['nom']] ?? 30;
        $start = now()->startOfDay();
        $end = $start->clone()->addDays($days);

        DB::transaction(function () use ($user, $selectedPlan, $start, $end) {
            // Expire l'ancien abonnement actif
            $user->subscriptions()->where('statut', 'actif')->update(['statut' => 'expiré']);

            Subscription::create([
                'user_id' => $user->id,
                'plan' => $selectedPlan['nom'],
                'prix' => $selectedPlan['prix'],
                'date_debut' => $start,
                'date_fin' => $end,
                'statut' => 'actif',
            ]);

            try {
                Mail::to($user->email)->send(new SubscriptionConfirmed($user, $selectedPlan['nom'], $end));
            } catch (\Exception $e) {
                Log::error('Subscription confirmation email failed: '.$e->getMessage());
            }
        });
    }

    public function hasActiveSubscription(User $user): bool
    {
        return $user->subscriptions()
            ->where('statut', 'actif')
            ->where('date_fin', '>=', now())
            ->exists();
    }
}
