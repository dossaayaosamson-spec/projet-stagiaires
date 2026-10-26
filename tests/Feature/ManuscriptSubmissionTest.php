<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ManuscriptSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_still_redirects_to_results_when_ai_analysis_fails(): void
    {
        $user = User::factory()->create(['role' => 'author']);

        $response = $this->actingAs($user)->post(route('manuscripts.store'), [
            'title' => 'Mon roman de test',
            'category' => 'Roman',
            'synopsis' => 'Un synopsis de test.',
            'document' => UploadedFile::fake()->create('manuscrit.pdf', 1024, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('manuscripts', [
            'user_id' => $user->id,
            'title' => 'Mon roman de test',
            'status' => 'pending',
        ]);
    }
}
