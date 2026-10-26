<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(),
            'author_id' => Author::factory(),
            'categorie' => fake()->word(),
            'resume' => fake()->paragraph(),
            'prix' => fake()->randomFloat(2, 5, 50),
            'pdf_path' => 'books/dummy.pdf',
            'is_free' => false,
            'format' => 'Numérique (PDF)',
            'stock' => null,
        ];
    }

    /** État : livre physique avec stock disponible. */
    public function physique(int $stock = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'format' => 'Physique',
            'disponibilite' => 'Physique',
            'stock' => $stock,
        ]);
    }

    /** État : livre numérique (défaut). */
    public function numerique(): static
    {
        return $this->state(fn (array $attributes) => [
            'format' => 'Numérique (PDF)',
            'disponibilite' => 'Numérique',
            'stock' => null,
        ]);
    }
}
