<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderInvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_order_invoice_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);
        $book = Book::factory()->create(['titre' => 'Le Souffle de la Terre']);

        $order = Order::factory()->create([
            'user_id' => $client->id,
            'statut' => 'confirmée',
            'type_commande' => 'physique',
            'nom_destinataire' => 'Kofi Mensah',
            'frais_livraison' => 1000,
            'total' => 6000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'quantite' => 1,
            'prix_unitaire' => 5000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.invoice', $order));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_non_admin_cannot_download_order_invoice(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $order = Order::factory()->create(['user_id' => $client->id]);

        $response = $this->actingAs($client)->get(route('admin.orders.invoice', $order));

        $response->assertForbidden();
    }
}
