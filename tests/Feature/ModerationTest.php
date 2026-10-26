<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use App\Models\UserSuspension;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test d'archivage par un admin.
     */
    public function test_admin_peut_archiver_commentaire(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $comment = Comment::factory()->create(['statut' => 'publié']);

        $response = $this->actingAs($admin)->post(route('admin.moderation.archive', $comment), [
            'motif' => 'Spam publicitaire répétitif.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'statut' => 'archivé',
        ]);

        $this->assertDatabaseHas('admin_action_logs', [
            'action' => 'archive_comment',
            'admin_id' => $admin->id,
        ]);
    }

    /**
     * Test de suspension d'un utilisateur.
     */
    public function test_admin_peut_suspendre_utilisateur(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($admin)->post(route('admin.users.suspend', $user), [
            'motif' => 'Comportement toxique répété.',
        ]);

        $this->assertDatabaseHas('user_suspensions', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
        ]);

        $this->assertTrue($user->fresh()->isSuspended());
    }

    /**
     * Test que le middleware bloque les utilisateurs suspendus.
     */
    public function test_middleware_bloque_utilisateur_suspendu(): void
    {
        $user = User::factory()->create();
        UserSuspension::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('comments.store'), [
            'contenu' => 'Tentative de publication.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('suspension');
    }

    /**
     * Test de résolution d'alias.
     */
    public function test_admin_peut_resoudre_alias(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'prenom' => 'Jean',
            'nom_famille' => 'Dupont',
            'alias' => 'LeMasque',
        ]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.moderation.resolve-alias'), [
            'alias' => 'LeMasque',
        ]);

        $response->assertJson([
            'found' => true,
            'nom_complet' => 'Jean Dupont',
        ]);
    }

    /**
     * Test de filtrage par alias dans la modération.
     */
    public function test_admin_peut_filtrer_commentaires_par_alias(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'alias' => 'Crimson',
        ]);
        Comment::factory()->create([
            'user_id' => $user->id,
            'contenu' => 'Super livre !',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.moderation.index', ['alias' => 'Crimson']));

        $response->assertOk();
        $response->assertSee('Super livre !');
    }
}
