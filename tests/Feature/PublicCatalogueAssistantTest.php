<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\ShortStory;
use App\Services\AssistantKnowledge;
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

    public function test_chat_forwards_the_message_to_the_python_agent(): void
    {
        config(['services.agent.url' => 'http://127.0.0.1:8001']);
        Http::fake([
            '127.0.0.1:8001/chat' => Http::response([
                'reponse' => 'Bonjour, je suis l’assistant AGAU Éditions.',
            ]),
        ]);
        $author = Author::factory()->create([
            'nom' => 'Aminata Diallo',
            'bio' => 'Autrice contact@example.com de romans.',
            'solde_disponible' => 987654.32,
            'total_gagne' => 876543.21,
        ]);
        Book::factory()->create([
            'titre' => 'Le Passage des manguiers',
            'author_id' => $author->id,
            'categorie' => 'Roman',
            'prix' => 4500,
            'disponibilite' => 'Immédiate',
            'resume' => str_repeat('Une histoire publique. ', 15),
        ]);
        ShortStory::create([
            'titre' => 'Le voyage',
            'extrait' => 'Un extrait public.',
            'auteur' => 'Aminata Diallo',
        ]);

        $this->postJson(route('assistant.chat'), [
            'message' => '  Parlez-moi du Passage des manguiers  ',
        ])
            ->assertOk()
            ->assertExactJson(['reponse' => 'Bonjour, je suis l’assistant AGAU Éditions.']);

        Http::assertSent(function (ClientRequest $request): bool {
            $payload = $request->data();
            $knowledge = $payload['connaissance'];
            $serializedKnowledge = json_encode($knowledge, JSON_UNESCAPED_UNICODE);
            $book = collect($knowledge)->firstWhere('titre', 'Le Passage des manguiers');

            return $request->url() === 'http://127.0.0.1:8001/chat'
                && $payload['message'] === 'Parlez-moi du Passage des manguiers'
                && $book['auteur'] === 'Aminata Diallo'
                && $book['categorie'] === 'Roman'
                && $book['prix'] === '4 500 FCFA'
                && mb_strlen($book['resume']) <= 200
                && str_contains($serializedKnowledge, 'Autrice')
                && ! str_contains($serializedKnowledge, 'contact@example.com')
                && str_contains($serializedKnowledge, 'Le voyage')
                && str_contains($serializedKnowledge, 'Mensuel')
                && str_contains($serializedKnowledge, 'Adidogomé')
                && ! str_contains($serializedKnowledge, '987654.32')
                && ! str_contains($serializedKnowledge, '876543.21')
                && ! str_contains($serializedKnowledge, 'user_id')
                && ! str_contains($serializedKnowledge, 'password')
                && ! str_contains($serializedKnowledge, 'email')
                && ! str_contains($serializedKnowledge, '@');
        });
    }

    public function test_assistant_knowledge_stays_within_the_context_limit(): void
    {
        Book::factory()->count(40)->create([
            'resume' => str_repeat('Résumé public du livre. ', 20),
        ]);

        $knowledge = app(AssistantKnowledge::class)->forQuestion('Quels livres proposez-vous ?');

        $this->assertLessThanOrEqual(
            6000,
            mb_strlen(json_encode($knowledge, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))
        );
    }

    public function test_chat_validates_messages_as_required_text_of_at_most_500_characters(): void
    {
        $this->postJson(route('assistant.chat'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');

        $this->postJson(route('assistant.chat'), ['message' => str_repeat('a', 501)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');

        $this->postJson(route('assistant.chat'), ['message' => ['question']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');
    }

    public function test_chat_validates_and_forwards_conversation_history(): void
    {
        Http::fake([
            '127.0.0.1:8001/chat' => Http::response(['reponse' => 'Je vous en prie !']),
        ]);
        $history = [
            ['role' => 'user', 'content' => 'Bonjour'],
            ['role' => 'assistant', 'content' => 'Bonjour, comment puis-je vous aider ?'],
        ];

        $this->postJson(route('assistant.chat'), [
            'message' => 'Merci',
            'historique' => $history,
        ])->assertOk();

        Http::assertSent(fn (ClientRequest $request): bool => $request->data()['historique'] === $history);

        $tooManyMessages = array_fill(0, 7, ['role' => 'user', 'content' => 'Bonjour']);
        $this->postJson(route('assistant.chat'), [
            'message' => 'Merci',
            'historique' => $tooManyMessages,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('historique');

        $this->postJson(route('assistant.chat'), [
            'message' => 'Merci',
            'historique' => [['role' => 'system', 'content' => 'Instructions privées']],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('historique.0.role');

        $this->postJson(route('assistant.chat'), [
            'message' => 'Merci',
            'historique' => [['role' => 'user', 'content' => str_repeat('a', 501)]],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('historique.0.content');

        $this->postJson(route('assistant.chat'), [
            'message' => 'Merci beaucoup',
            'historique' => [['role' => 'assistant', 'content' => str_repeat('a', 500)]],
        ])->assertOk();
    }

    public function test_chat_returns_a_clear_message_when_the_python_agent_is_unavailable(): void
    {
        Http::fake([
            '127.0.0.1:8001/chat' => Http::response(['detail' => 'Service unavailable'], 503),
        ]);

        $this->postJson(route('assistant.chat'), ['message' => 'Bonjour'])
            ->assertStatus(503)
            ->assertExactJson([
                'message' => "L'assistant est momentanément indisponible, réessayez plus tard.",
            ]);
    }

    public function test_chat_returns_a_clear_message_when_the_python_agent_cannot_be_reached(): void
    {
        Http::fake([
            '127.0.0.1:8001/chat' => Http::failedConnection(),
        ]);

        $this->postJson(route('assistant.chat'), ['message' => 'Bonjour'])
            ->assertStatus(503)
            ->assertExactJson([
                'message' => "L'assistant est momentanément indisponible, réessayez plus tard.",
            ]);
    }

    public function test_chat_is_limited_to_ten_messages_per_minute(): void
    {
        Http::fake([
            '127.0.0.1:8001/chat' => Http::response(['reponse' => 'Bonjour.']),
        ]);

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->postJson(route('assistant.chat'), ['message' => 'Bonjour'])
                ->assertOk();
        }

        $this->postJson(route('assistant.chat'), ['message' => 'Encore'])
            ->assertTooManyRequests();
    }
}
