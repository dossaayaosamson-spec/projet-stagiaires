<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\ShortStory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicCatalogueAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_assistant_is_only_displayed_on_the_requested_public_pages(): void
    {
        $book = Book::factory()->create();
        $story = ShortStory::create([
            'titre' => 'Une histoire publique',
            'extrait' => 'Un court extrait de démonstration.',
            'auteur' => 'Auteur de test',
        ]);
        $publicPaths = [
            '/',
            '/catalogue',
            '/short-stories',
            '/login',
            '/register',
            route('front.livres.show', $book->frontRouteParams()),
            route('front.stories.show', $story->id),
        ];

        foreach ($publicPaths as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('catalogue-assistant-launcher');
        }

        $this->get('/contact')
            ->assertOk()
            ->assertDontSee('catalogue-assistant-launcher');
    }

    public function test_assistant_answers_using_only_public_catalogue_information(): void
    {
        config([
            'services.openai.api_key' => 'fake-test-key',
            'services.openai.model' => 'gpt-5.4-mini',
        ]);
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Le livre coûte 4 500 FCFA.']],
                ],
            ]),
        ]);

        $author = Author::factory()->create(['nom' => 'Aminata Diallo']);
        Book::factory()->create([
            'titre' => 'Le Passage des manguiers',
            'author_id' => $author->id,
            'prix' => 4500,
            'disponibilite' => 'Immédiate',
        ]);

        $this->postJson(route('public-assistant.ask'), [
            'message' => 'Quel est le prix du livre ?',
        ])
            ->assertOk()
            ->assertExactJson(['answer' => 'Le livre coûte 4 500 FCFA.']);

        Http::assertSent(function (ClientRequest $request): bool {
            $payload = $request->data();
            $systemMessage = $payload['messages'][0]['content'];

            return $request->url() === 'https://api.openai.com/v1/chat/completions'
                && $payload['model'] === 'gpt-5.4-mini'
                && $payload['store'] === false
                && str_contains($systemMessage, 'Le Passage des manguiers')
                && str_contains($systemMessage, 'Aminata Diallo')
                && $payload['messages'][1]['content'] === 'Quel est le prix du livre ?';
        });
    }

    public function test_missing_api_key_returns_an_explicit_unavailable_response(): void
    {
        config(['services.openai.api_key' => null]);
        Http::fake();

        $this->postJson(route('public-assistant.ask'), ['message' => 'Quels livres proposez-vous ?'])
            ->assertStatus(503)
            ->assertJsonPath('message', 'L’assistant est momentanément indisponible. Réessayez plus tard.');

        Http::assertNothingSent();
    }

    public function test_provider_failure_returns_an_explicit_unavailable_response(): void
    {
        config(['services.openai.api_key' => 'fake-test-key']);
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response(['error' => 'provider failure'], 429),
        ]);

        $this->postJson(route('public-assistant.ask'), ['message' => 'Quels livres proposez-vous ?'])
            ->assertStatus(503)
            ->assertJsonPath('message', 'L’assistant est momentanément indisponible. Réessayez plus tard.');
    }

    public function test_question_is_required_and_limited_to_one_thousand_characters(): void
    {
        $this->postJson(route('public-assistant.ask'), ['message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');

        $this->postJson(route('public-assistant.ask'), ['message' => str_repeat('a', 1001)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');
    }
}
