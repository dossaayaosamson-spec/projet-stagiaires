<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_session_expires_after_5_minutes_inactivity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Première requête : initialise le timestamp de dernière activité
        $this->actingAs($admin)
            ->withSession(['last_activity_timestamp' => time() - 305]) // Inactif depuis > 5 min (305s)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_admin_session_remains_active_within_5_minutes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->withSession(['last_activity_timestamp' => time() - 100]) // 100s d'inactivité (< 300s)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->assertAuthenticatedAs($admin);
    }

    public function test_reader_session_active_at_7_minutes_but_expires_after_10_minutes(): void
    {
        $reader = User::factory()->create(['role' => 'client']);

        // Valide à 7 minutes (420s < 600s)
        $this->actingAs($reader)
            ->withSession(['last_activity_timestamp' => time() - 420])
            ->get(route('dashboard'))
            ->assertOk();

        // Expiré après 10 minutes (605s > 600s)
        $this->actingAs($reader)
            ->withSession(['last_activity_timestamp' => time() - 605])
            ->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->assertGuest();
    }
}
