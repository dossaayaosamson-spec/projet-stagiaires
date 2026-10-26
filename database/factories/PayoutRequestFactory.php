<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayoutRequest>
 */
class PayoutRequestFactory extends Factory
{
    protected $model = PayoutRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => Author::factory(),
            'montant' => fake()->randomFloat(2, 10000, 150000),
            'moyen_paiement' => fake()->randomElement(['tmoney', 'flooz', 'virement_bancaire']),
            'coordonnees_paiement' => '+228 90 00 00 00',
            'statut' => 'en_attente',
            'reference_transaction' => null,
            'admin_notes' => null,
            'validated_by' => null,
            'validated_at' => null,
        ];
    }

    public function validated(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'valide',
            'reference_transaction' => 'TXN-'.fake()->numerify('######'),
            'validated_by' => User::factory(),
            'validated_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'rejete',
            'admin_notes' => 'Coordonnées invalides',
            'validated_by' => User::factory(),
            'validated_at' => now(),
        ]);
    }
}
