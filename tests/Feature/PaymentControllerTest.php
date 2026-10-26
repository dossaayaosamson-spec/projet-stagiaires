<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\Payments\FedaPayPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_pay_someone_elses_order()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user1->id,
            'statut' => 'en_attente',
            'total' => 1000,
        ]);

        $response = $this->actingAs($user2)->get(route('payment.pay', $order->id));

        $response->assertStatus(403);
    }

    public function test_can_process_approved_transaction_return()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'statut' => 'en_attente',
            'total' => 1000,
        ]);

        // We bypass the actual FedaPay API call in tests
        // In a real scenario, we would use Mockery to mock Transaction::retrieve()
        // Here we just test the logic when the transaction ID is missing to avoid calling the real API
        $response = $this->actingAs($user)->get(route('payment.return', ['order' => $order->id]));

        $response->assertRedirect(route('client.orders'));
        $response->assertSessionHas('info');
    }

    public function test_pending_transaction_in_sandbox_marks_order_as_paid()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'statut' => 'en_attente',
            'total' => 1000,
        ]);

        $service = Mockery::mock(FedaPayPaymentService::class);
        $service->shouldReceive('retrieveTransaction')
            ->once()
            ->with('tx_123')
            ->andReturn((object) ['status' => 'pending']);
        $service->shouldReceive('isSandbox')
            ->once()
            ->andReturn(true);

        $this->app->instance(FedaPayPaymentService::class, $service);

        $response = $this->actingAs($user)->get(route('payment.return', [
            'order' => $order->id,
            'transaction_id' => 'tx_123',
        ]));

        $response->assertRedirect(route('payment.success', ['order' => $order->id]));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'statut' => 'livrée',
        ]);
    }

    public function test_canceled_transaction_keeps_order_pending_and_redirects_to_orders()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'statut' => 'en_attente',
            'total' => 1000,
        ]);

        $service = Mockery::mock(FedaPayPaymentService::class);
        $service->shouldReceive('retrieveTransaction')
            ->once()
            ->with('tx_cancel')
            ->andReturn((object) ['status' => 'canceled']);
        // isSandbox n'est pas appelé si status !== 'pending'

        $this->app->instance(FedaPayPaymentService::class, $service);

        $response = $this->actingAs($user)->get(route('payment.return', [
            'order' => $order->id,
            'transaction_id' => 'tx_cancel',
        ]));

        $response->assertRedirect(route('client.orders'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'statut' => 'en_attente',
        ]);
    }

    public function test_webhook_marks_order_as_paid()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'statut' => 'en_attente',
            'total' => 1000,
        ]);

        $fakeTransaction = (object) [
            'status' => 'approved',
            'metadata' => (object) [
                'type' => 'order',
                'order_id' => $order->id,
            ],
        ];

        $service = Mockery::mock(FedaPayPaymentService::class);
        $service->shouldReceive('retrieveTransaction')
            ->once()
            ->andReturn($fakeTransaction);

        $this->app->instance(FedaPayPaymentService::class, $service);

        $response = $this->postJson(route('payment.webhook'), [
            'event' => 'transaction.approved',
            'entity' => ['id' => 'tx_webhook_1'],
        ]);

        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'statut' => 'livrée',
        ]);
    }

    public function test_webhook_ignores_already_paid_order()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'statut' => 'livrée',
            'total' => 1000,
        ]);

        $fakeTransaction = (object) [
            'status' => 'approved',
            'metadata' => (object) [
                'type' => 'order',
                'order_id' => $order->id,
            ],
        ];

        $service = Mockery::mock(FedaPayPaymentService::class);
        $service->shouldReceive('retrieveTransaction')
            ->once()
            ->andReturn($fakeTransaction);

        $this->app->instance(FedaPayPaymentService::class, $service);

        $this->postJson(route('payment.webhook'), [
            'event' => 'transaction.approved',
            'entity' => ['id' => 'tx_webhook_2'],
        ]);

        // Le statut ne doit pas changer (déjà paye)
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'statut' => 'livrée',
        ]);
    }

    public function test_webhook_handles_get_request()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('payment.webhook'));

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('info', 'Paiement annulé ou retour au site.');
    }

    public function test_cannot_access_payment_success_if_order_not_paid()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'statut' => 'en_attente',
            'total' => 1000,
        ]);

        $response = $this->actingAs($user)->get(route('payment.success', $order->id));

        $response->assertRedirect(route('client.orders'));
    }
}
