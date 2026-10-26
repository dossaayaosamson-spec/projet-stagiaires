<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_become_author_form(): void
    {
        $response = $this->get(route('client.become-author'));

        $response->assertRedirect(route('login'));
    }

    public function test_client_can_view_become_author_form(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('client.become-author'));

        $response->assertStatus(200);
        $response->assertSeeText("Postuler en tant qu'Auteur", false);
        $response->assertSee('name="title"', false);
        $response->assertSee('name="manuscript_file"', false);
    }

    public function test_author_is_redirected_to_author_dashboard(): void
    {
        $authorUser = User::factory()->create([
            'role' => 'auteur',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($authorUser)->get(route('client.become-author'));

        $response->assertRedirect(route('author.dashboard'));
    }

    public function test_client_can_submit_author_application_successfully(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'client',
            'email_verified_at' => now(),
            'prenom' => 'Koffi',
            'nom_famille' => 'Mensah',
        ]);

        $pdf = UploadedFile::fake()->create('mon_manuscrit.pdf', 500, 'application/pdf');
        $portrait = UploadedFile::fake()->image('portrait.jpg', 300, 300);

        $payload = [
            'pen_name' => 'Koffi Plume',
            'bio' => 'Écrivain togolais passionné par la poésie et la prose contemporaine.',
            'portrait' => $portrait,
            'country' => 'Togo',
            'city' => 'Lomé',
            'social_links' => 'https://twitter.com/koffiplume',
            'title' => 'Les Lueurs de la Lagune',
            'category' => 'Roman',
            'synopsis' => 'Un roman initiatique captivant retraçant le voyage intérieur d\'un jeune pêcheur face à la modernité.',
            'intent_note' => 'Je souhaite transmettre un regard authentique sur les traditions en mutation.',
            'manuscript_file' => $pdf,
            'certified_original' => '1',
        ];

        $response = $this->actingAs($user)->post(route('client.become-author.store'), $payload);

        $response->assertRedirect(route('client.become-author.status'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('manuscripts', [
            'user_id' => $user->id,
            'submission_source' => 'candidature_auteur',
            'title' => 'Les Lueurs de la Lagune',
            'pen_name' => 'Koffi Plume',
            'category' => 'Roman',
            'city' => 'Lomé',
            'status' => 'pending',
            'certified_original' => 1,
        ]);

        $manuscript = Manuscript::where('title', 'Les Lueurs de la Lagune')->first();
        $this->assertNotNull($manuscript);
        Storage::disk('public')->assertExists($manuscript->file_path);
        Storage::disk('public')->assertExists($manuscript->portrait_path);
    }

    public function test_client_cannot_submit_duplicate_pending_application(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        // Manuscrit déjà en cours
        Manuscript::create([
            'user_id' => $user->id,
            'submission_source' => 'candidature_auteur',
            'title' => 'Premier Manuscrit',
            'category' => 'Roman',
            'synopsis' => 'Synopsis du premier manuscrit soumis par le client.',
            'file_path' => 'manuscripts/candidatures/test.pdf',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Visiter la page de formulaire doit rediriger vers le statut
        $response = $this->actingAs($user)->get(route('client.become-author'));
        $response->assertRedirect(route('client.become-author.status'));

        // Tenter de soumettre à nouveau doit être bloqué
        $pdf = UploadedFile::fake()->create('deuxieme.pdf', 300, 'application/pdf');
        $submitResponse = $this->actingAs($user)->post(route('client.become-author.store'), [
            'title' => 'Deuxième Manuscrit',
            'bio' => 'Une autre biographie d\'auteur.',
            'country' => 'Togo',
            'city' => 'Lomé',
            'category' => 'Roman',
            'synopsis' => 'Un autre synopsis suffisamment long pour le formulaire.',
            'intent_note' => 'Une note d\'intention pour le deuxième manuscrit.',
            'manuscript_file' => $pdf,
            'certified_original' => '1',
        ]);

        $submitResponse->assertRedirect(route('client.become-author.status'));
        $this->assertDatabaseMissing('manuscripts', ['title' => 'Deuxième Manuscrit']);
    }

    public function test_application_requires_valid_data(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('client.become-author.store'), []);

        $response->assertSessionHasErrors([
            'bio',
            'country',
            'city',
            'title',
            'category',
            'synopsis',
            'intent_note',
            'manuscript_file',
            'certified_original',
        ]);
    }

    public function test_client_can_view_status_page(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        Manuscript::create([
            'user_id' => $user->id,
            'submission_source' => 'candidature_auteur',
            'title' => 'Livre en Examen',
            'category' => 'Poésie',
            'synopsis' => 'Recueil poétique célébrant la terre et l\'humain.',
            'intent_note' => 'Partager des vers chaleureux.',
            'file_path' => 'manuscripts/candidatures/poesie.pdf',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('client.become-author.status'));

        $response->assertStatus(200);
        $response->assertSee('Livre en Examen');
        $response->assertSee('Poésie');
        $response->assertSee('Reçu — En attente d\'examen');
    }
}
