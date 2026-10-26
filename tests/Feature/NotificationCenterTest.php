<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_service_returns_notifications_for_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $order = Order::create([
            'user_id' => $admin->id,
            'total' => 10000,
            'statut' => 'en_attente_verification',
        ]);

        $service = new NotificationService;
        $notifications = $service->getNotificationsForUser($admin);

        $this->assertNotEmpty($notifications);
        $this->assertTrue($notifications->contains('id', 'order_'.$order->id));
    }
}
