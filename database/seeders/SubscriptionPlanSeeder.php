<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Ce seeder stocke les définitions des plans d'abonnement
 * dans une table dédiée (subscription_plans) à créer si besoin,
 * ou simplement ici pour référence. Les abonnements utilisateurs
 * (table subscriptions) sont créés lors de la souscription.
 *
 * Pour l'instant nous utilisons une simple config pour référence.
 */
class SubscriptionPlanSeeder extends Seeder
{
    public static array $plans = [];

    /**
     * Initialiser les plans si nécessaire (pourrait être utilisé pour insérer en base)
     */
    public function __construct()
    {
        self::$plans = config('plans', []);
    }

    public function run(): void
    {
        // Les plans sont utilisés comme référence dans les contrôleurs
        // via SubscriptionPlanSeeder::$plans ou config/agau.php
        $this->command->info('Plans d\'abonnement chargés en mémoire comme référence.');
    }
}
