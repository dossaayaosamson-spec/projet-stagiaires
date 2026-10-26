<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an admin can create an event with an encoded registration link.
     */
    public function test_admin_can_store_event_with_encoded_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $rawUrl = 'https://example.com/join?token=abc_123+xyz';
        $encodedUrl = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($rawUrl));

        $response = $this->actingAs($admin)->post(route('admin.events.store'), [
            'titre' => 'Un super événement',
            'description' => 'Description de l\'événement',
            'date_evenement' => '2026-06-01 10:00:00',
            'lien_inscription' => $encodedUrl,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', [
            'titre' => 'Un super événement',
            'lien_inscription' => $rawUrl,
        ]);
    }

    /**
     * Test that an admin can update an event with an encoded registration link.
     */
    public function test_admin_can_update_event_with_encoded_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::create([
            'titre' => 'Événement initial',
            'description' => 'Description initiale',
            'date_evenement' => '2026-06-01 10:00:00',
            'lien_inscription' => 'https://old.com',
            'is_active' => true,
        ]);

        $newRawUrl = 'https://new-link.com/register?param=value_with+plus/slash';
        $newEncodedUrl = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($newRawUrl));

        $response = $this->actingAs($admin)->put(route('admin.events.update', $event), [
            'titre' => 'Événement mis à jour',
            'description' => 'Nouvelle description',
            'date_evenement' => '2026-06-02 12:00:00',
            'lien_inscription' => $newEncodedUrl,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'titre' => 'Événement mis à jour',
            'lien_inscription' => $newRawUrl,
        ]);
    }

    /**
     * Test that non-admin users cannot access event store route.
     */
    public function test_non_admin_cannot_store_event(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)->post(route('admin.events.store'), [
            'titre' => 'Tentative',
            'date_evenement' => '2026-06-01 10:00:00',
        ]);

        $response->assertStatus(403);
    }
}
