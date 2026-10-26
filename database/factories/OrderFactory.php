<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'statut' => 'en_attente',
            'total' => 0,
            'type_commande' => 'numerique',
        ];
    }

    /** État : commande physique avec adresse de livraison. */
    public function physique(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_commande' => 'physique',
            'nom_destinataire' => fake()->name(),
            'telephone_livraison' => fake()->phoneNumber(),
            'adresse_livraison' => fake()->streetAddress(),
            'ville_livraison' => fake()->city(),
            'pays_livraison' => 'Togo',
        ]);
    }

    /** État : commande confirmée. */
    public function confirmee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'confirmée',
        ]);
    }
}
