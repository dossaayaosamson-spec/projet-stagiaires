<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoyaltyService
{
    /**
     * Traite et crédite les redevances d'une commande payée aux auteurs concernés.
     * Cette méthode est idempotente au niveau des calculs si nécessaire.
     */
    public function processOrderRoyalties(Order $order): void
    {
        $order->loadMissing('items.book.author', 'items.book.activeContract');

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $book = $item->book;
                if (! $book) {
                    continue;
                }

                $author = $book->author;
                if (! $author) {
                    continue;
                }

                $contract = $book->activeContract;
                if (! $contract || ! $contract->isActive()) {
                    Log::info("RoyaltyService: Aucun contrat actif pour le livre #{$book->id} (Auteur #{$author->id}).");
                    continue;
                }

                $taux = (float) $contract->taux_redevance;
                if ($taux <= 0) {
                    continue;
                }

                $quantite = (int) $item->quantite;
                $prixUnitaire = (float) $item->prix_unitaire;
                $montantVente = $quantite * $prixUnitaire;

                // Formule : Quantité × Prix × (Taux / 100)
                $royalty = round($montantVente * ($taux / 100), 2);

                if ($royalty > 0) {
                    $author->increment('solde_disponible', $royalty);
                    $author->increment('total_gagne', $royalty);

                    Log::info("RoyaltyService: Crédit de {$royalty} FCFA pour l'auteur #{$author->id} (Livre #{$book->id}, Contrat #{$contract->id}, Commande #{$order->id}).");
                }
            }
        });
    }
}
