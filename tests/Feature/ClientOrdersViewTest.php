<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientOrdersViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_orders_page_displays_book_titles_and_sequential_numbers(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $author = Author::create([
            'user_id' => $user->id,
            'nom' => 'Camara Laye',
            'initiales' => 'CL',
        ]);

        $book = Book::create([
            'author_id' => $author->id,
            'titre' => 'L\'Enfant Noir',
            'auteur' => 'Camara Laye',
            'isbn' => '9781234567890',
            'prix_papier' => 5000,
            'statut' => 'disponible',
            'stock' => 10,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 5000,
            'statut' => 'en_attente',
            'payment_method' => 'manual',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'quantite' => 1,
            'prix_unitaire' => 5000,
        ]);

        $response = $this->actingAs($user)->get(route('client.orders'));

        $response->assertStatus(200);
        $response->assertSee('#1');
        $response->assertSee('L\'Enfant Noir');
    }
}
