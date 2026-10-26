<?php

namespace Tests\Feature;

use App\Mail\OrderShipped;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderShipmentTest extends TestCase
{
    use RefreshDatabase;

    private function createPhysicalOrderWithAdmin(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $book = Book::factory()->physique()->create(['prix' => 5000]);

        $order = Order::factory()->physique()->confirmee()->create([
            'user_id' => $user->id,
            'total' => 5000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'quantite' => 1,
            'prix_unitaire' => 5000,
        ]);

        return [$admin, $order, $user];
    }

    public function test_admin_can_ship_physical_order(): void
    {
        Mail::fake();

        [$admin, $order, $user] = $this->createPhysicalOrderWithAdmin();

        $response = $this->actingAs($admin)->post(route('admin.orders.ship', $order), [
            'nom_livreur' => 'Kofi Mensah',
            'telephone_livreur' => '+228 92 22 22 22',
            'frais_livraison' => 1500,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('en_route', $order->statut);
        $this->assertEquals('Kofi Mensah', $order->nom_livreur);
        $this->assertEquals(1500, $order->frais_livraison);
        $this->assertEquals(6500, $order->total); // 5000 + 1500
    }

    public function test_ship_sends_email_to_client(): void
    {
        Mail::fake();

        [$admin, $order, $user] = $this->createPhysicalOrderWithAdmin();

        $this->actingAs($admin)->post(route('admin.orders.ship', $order), [
            'nom_livreur' => 'Kofi Mensah',
            'telephone_livreur' => '+228 92 22 22 22',
            'frais_livraison' => 500,
        ]);

        Mail::assertSent(OrderShipped::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_ship_requires_minimum_500_frais(): void
    {
        [$admin, $order] = $this->createPhysicalOrderWithAdmin();

        $response = $this->actingAs($admin)->post(route('admin.orders.ship', $order), [
            'nom_livreur' => 'Kofi Mensah',
            'telephone_livreur' => '+228 92 22 22 22',
            'frais_livraison' => 200, // sous le minimum
        ]);

        $response->assertSessionHasErrors(['frais_livraison']);
    }

    public function test_ship_requires_livreur_name_and_phone(): void
    {
        [$admin, $order] = $this->createPhysicalOrderWithAdmin();

        $response = $this->actingAs($admin)->post(route('admin.orders.ship', $order), [
            'frais_livraison' => 500,
        ]);

        $response->assertSessionHasErrors(['nom_livreur', 'telephone_livreur']);
    }
}
