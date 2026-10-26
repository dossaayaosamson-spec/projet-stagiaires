<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->name(),
            'initiales' => fake()->lexify('???'),
            'bio' => fake()->paragraph(),
            'nb_livres' => 0,
            'solde_disponible' => 0.00,
            'total_gagne' => 0.00,
            'total_retire' => 0.00,
        ];
    }
}
