<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de publication d'un commentaire classique.
     */
    public function test_utilisateur_peut_publier_commentaire(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Book::class,
            'commentable_id' => $book->id,
            'contenu' => 'Superbe livre, j\'ai adoré la plume de l\'auteur !',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'contenu' => 'Superbe livre, j\'ai adoré la plume de l\'auteur !',
            'statut' => 'publié',
        ]);
    }

    /**
     * Test de l'anti-usurpation (alias vs nom d'auteur).
     */
    public function test_interdit_alias_usurpant_nom_auteur(): void
    {
        $user = User::factory()->create();

        // Simuler un auteur existant via le service (ou créer en DB si la table existe)
        \DB::table('authors')->insert([
            'nom' => 'Chinua Achebe',
            'initiales' => 'CA',
            'bio' => '...',
            'user_id' => User::factory()->create(['prenom' => 'Chinua', 'nom_famille' => 'Achebe'])->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('comments.store'), [
            'alias' => 'Chinua Achebe',
            'contenu' => 'Je suis le vrai auteur.',
        ]);

        $response->assertSessionHasErrors('alias');
        $this->assertDatabaseMissing('users', ['alias' => 'Chinua Achebe']);
    }

    /**
     * Test de l'anti-spam (rate limit).
     */
    public function test_anti_spam_bloque_publications_trop_rapides(): void
    {
        $user = User::factory()->create();

        // On consomme le rate limit
        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)->post(route('comments.store'), ['contenu' => "Test $i"]);
        }

        $response = $this->actingAs($user)->post(route('comments.store'), [
            'contenu' => 'Sixième commentaire trop rapide.',
        ]);

        $response->assertSessionHasErrors('rate_limit');
    }

    /**
     * Test de détection de contenu haineux.
     */
    public function test_signale_automatiquement_contenu_haineux(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('comments.store'), [
            'contenu' => 'Ceci est un message de haine avec des insultes.', // Supposant que "insultes" est dans la liste
        ]);

        $this->assertDatabaseHas('comments', [
            'is_flagged' => true,
        ]);

        $comment = Comment::where('user_id', $user->id)->first();
        $this->assertStringContainsString('insulte', $comment->flag_reason);
    }

    /**
     * Test de réponse à un commentaire (threading).
     */
    public function test_peut_repondre_a_un_commentaire(): void
    {
        $user = User::factory()->create();
        $parent = Comment::factory()->create();

        $response = $this->actingAs($user)->post(route('comments.store'), [
            'parent_id' => $parent->id,
            'contenu' => 'Je suis d\'accord avec vous !',
        ]);

        $this->assertDatabaseHas('comments', [
            'parent_id' => $parent->id,
            'contenu' => 'Je suis d\'accord avec vous !',
        ]);
    }
}
