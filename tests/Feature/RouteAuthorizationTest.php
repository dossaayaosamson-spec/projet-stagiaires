<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Manuscript;
use App\Models\User;
use App\Models\UserSuspension;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_when_accessing_admin_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_non_author_user_is_redirected_from_author_dashboard()
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)->get(route('author.dashboard'));

        $response->assertRedirect(route('home'));
    }

    public function test_author_user_can_access_author_dashboard()
    {
        $author = User::factory()->create(['role' => 'author']);
        Author::factory()->create(['user_id' => $author->id]);

        $response = $this->actingAs($author)->get(route('author.dashboard'));

        $response->assertOk();
    }

    public function test_author_dashboard_lists_submitted_manuscripts_and_submission_modal()
    {
        $author = User::factory()->create(['role' => 'author']);
        Author::factory()->create(['user_id' => $author->id]);

        Manuscript::create([
            'user_id' => $author->id,
            'title' => 'Roman test',
            'author_name' => $author->nom_complet,
            'category' => 'Roman',
            'synopsis' => 'Synopsis test',
            'file_path' => 'manuscripts/test.pdf',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($author)->get(route('author.dashboard'));

        $response->assertOk();
        $response->assertSee('Mes manuscrits soumis');
        $response->assertSee('Roman test');
        $response->assertSee('Soumettre un manuscrit');
    }

    public function test_guest_is_redirected_to_login_when_accessing_client_dashboard()
    {
        $response = $this->get(route('client.library'));

        $response->assertRedirect(route('login'));
    }

    public function test_suspended_user_cannot_post_comment()
    {
        $user = User::factory()->create();
        UserSuspension::factory()->create([
            'user_id' => $user->id,
            'lifted_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('comments.store'), [
            'contenu' => 'Un commentaire de test.',
        ]);

        $response->assertRedirect();
        // CheckSuspended utilise withErrors(), la clé est 'suspension'
        $response->assertSessionHasErrors('suspension');
    }
}
