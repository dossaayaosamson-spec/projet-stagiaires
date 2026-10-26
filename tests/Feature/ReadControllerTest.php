<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_pdf()
    {
        $book = Book::factory()->create(['pdf_path' => 'books/dummy.pdf']);

        $response = $this->get(route('read.pdf', $book->id));

        $response->assertStatus(401);
    }

    public function test_authenticated_user_without_access_cannot_read_pdf()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['pdf_path' => 'books/dummy.pdf', 'is_free' => false]);

        $response = $this->actingAs($user)->get(route('read.pdf', $book->id));

        $response->assertStatus(403);
    }

    public function test_authenticated_user_with_paid_order_can_read_pdf()
    {
        Storage::fake('local');
        Storage::disk('local')->put('books/dummy.pdf', 'dummy content');

        $user = User::factory()->create();
        $book = Book::factory()->create(['pdf_path' => 'books/dummy.pdf', 'is_free' => false]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'statut' => 'confirmée',
            'total' => $book->prix,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'quantite' => 1,
            'prix_unitaire' => $book->prix,
        ]);

        $response = $this->actingAs($user)->get(route('read.pdf', $book->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_free_book_can_be_read_by_authenticated_user()
    {
        Storage::fake('local');
        Storage::disk('local')->put('books/free.pdf', 'free content');

        $user = User::factory()->create();
        $book = Book::factory()->create(['pdf_path' => 'books/free.pdf', 'is_free' => true]);

        $response = $this->actingAs($user)->get(route('read.pdf', $book->id));

        $response->assertStatus(200);
    }

    public function test_free_book_can_be_read_without_authentication()
    {
        Storage::fake('local');
        Storage::disk('local')->put('books/free.pdf', 'free content');

        $book = Book::factory()->create(['pdf_path' => 'books/free.pdf', 'is_free' => true, 'free_until' => null]);

        $response = $this->get(route('read.pdf', $book->id));

        $response->assertStatus(200);
    }

    public function test_expired_free_book_requires_payment()
    {
        $book = Book::factory()->create([
            'pdf_path' => 'books/expired.pdf',
            'is_free' => true,
            'free_until' => now()->subDay(),
        ]);

        $response = $this->get(route('read.pdf', $book->id));

        $response->assertStatus(401);
    }
}
