<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhysicalOrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_physical_cart_redirects_to_delivery_form_on_checkout(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $book = Book::factory()->physique()->create(['prix' => 2000]);

        $this->actingAs($user)->post(route('cart.add', $book->id));

        // Le POST /checkout doit rediriger vers le formulaire livraison si pas d'infos en session
        $response = $this->actingAs($user)->post(route('cart.checkout'));

        $response->assertRedirect(route('cart.delivery'));
    }

    public function test_delivery_form_requires_all_fields(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('cart.delivery.store'), []);

        $response->assertSessionHasErrors([
            'nom_destinataire',
            'telephone_livraison',
            'adresse_livraison',
            'ville_livraison',
            'pays_livraison',
        ]);
    }

    public function test_checkout_creates_physical_order_with_delivery_info(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $book = Book::factory()->physique(10)->create(['prix' => 5000]);

        $this->actingAs($user)->post(route('cart.add', $book->id));

        // Stocker les infos de livraison en session
        $this->actingAs($user)->post(route('cart.delivery.store'), [
            'nom_destinataire' => 'Jean Dupont',
            'telephone_livraison' => '+228 90 00 00 00',
            'adresse_livraison' => 'Quartier Bè',
            'ville_livraison' => 'Lomé',
            'pays_livraison' => 'Togo',
        ]);

        $this->actingAs($user)->post(route('cart.checkout'));

        /** @var Order|null $order */
        $order = Order::query()->where('user_id', $user->id)->first();

        $this->assertNotNull($order);
        $this->assertEquals('physique', $order->type_commande);
        $this->assertEquals('Jean Dupont', $order->nom_destinataire);
        $this->assertEquals('Lomé', $order->ville_livraison);
    }

    public function test_checkout_decrements_stock_for_physical_book(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $book = Book::factory()->physique(5)->create(['prix' => 3000]);

        $this->actingAs($user)->post(route('cart.add', $book->id));

        $this->actingAs($user)->post(route('cart.delivery.store'), [
            'nom_destinataire' => 'Marie Doe',
            'telephone_livraison' => '+228 91 11 11 11',
            'adresse_livraison' => 'Rue X',
            'ville_livraison' => 'Kpalimé',
            'pays_livraison' => 'Togo',
        ]);

        $this->actingAs($user)->post(route('cart.checkout'));

        $book->refresh();
        $this->assertEquals(4, $book->stock);
    }
}
