<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => Author::factory(),
            'book_id' => null,
            'manuscript_id' => null,
            'type_contrat' => fake()->randomElement(['compte_editeur', 'distribution_numerique']),
            'taux_redevance' => fake()->randomElement([10.00, 12.50, 15.00, 40.00, 50.00]),
            'date_debut' => now()->toDateString(),
            'date_fin' => null,
            'fichier_contrat_pdf' => null,
            'statut' => 'actif',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'actif',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_attente_signature',
        ]);
    }
}
