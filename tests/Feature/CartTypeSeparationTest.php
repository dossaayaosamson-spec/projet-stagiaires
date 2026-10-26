<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTypeSeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_add_physical_book_to_digital_cart(): void
    {
        $user = User::factory()->create();
        $digitalBook = Book::factory()->numerique()->create(['prix' => 1000]);
        $physicalBook = Book::factory()->physique()->create(['prix' => 2000]);

        // Ajouter un livre numérique au panier
        $this->actingAs($user)
            ->post(route('cart.add', $digitalBook->id))
            ->assertRedirect();

        // Tenter d'ajouter un livre physique : doit être bloqué
        $response = $this->actingAs($user)
            ->post(route('cart.add', $physicalBook->id));

        $response->assertSessionHas('error');
        $this->assertStringContainsString('numériques', session('error'));
    }

    public function test_cannot_add_digital_book_to_physical_cart(): void
    {
        $user = User::factory()->create();
        $physicalBook = Book::factory()->physique()->create(['prix' => 2000]);
        $digitalBook = Book::factory()->numerique()->create(['prix' => 1000]);

        // Ajouter un livre physique au panier
        $this->actingAs($user)
            ->post(route('cart.add', $physicalBook->id))
            ->assertRedirect();

        // Tenter d'ajouter un livre numérique : doit être bloqué
        $response = $this->actingAs($user)
            ->post(route('cart.add', $digitalBook->id));

        $response->assertSessionHas('error');
    }

    public function test_cannot_add_out_of_stock_physical_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->physique(0)->create(); // stock = 0

        $response = $this->actingAs($user)
            ->post(route('cart.add', $book->id));

        $response->assertSessionHas('error');
        $this->assertStringContainsString('stock', session('error'));
    }

    public function test_can_add_physical_book_with_stock(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->physique(5)->create();

        $response = $this->actingAs($user)
            ->post(route('cart.add', $book->id));

        $response->assertSessionHas('success');
    }

    public function test_cart_index_shows_physical_badge(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->physique()->create();

        $this->actingAs($user)->post(route('cart.add', $book->id));

        $response = $this->actingAs($user)->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee('Commande physique');
    }
}
