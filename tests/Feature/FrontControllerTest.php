<?php

namespace Tests\Feature;

use App\Mail\ContactConfirmation;
use App\Mail\ContactMessage;
use App\Models\Author;
use App\Models\Book;
use App\Models\ShortStory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FrontControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_cover_url_supports_test_covers_and_existing_storage_paths(): void
    {
        $testCover = Book::make(['cover_path' => 'images/covers-test/example.svg']);
        $storedCover = Book::make(['cover_path' => 'covers/example.jpg']);
        $missingCover = Book::make(['cover_path' => null]);

        $this->assertSame(asset('images/covers-test/example.svg'), $testCover->cover_url);
        $this->assertSame(asset('storage/covers/example.jpg'), $storedCover->cover_url);
        $this->assertNull($missingCover->cover_url);
    }

    public function test_catalogue_page_works(): void
    {
        $author = Author::factory()->create();
        Book::factory()->count(5)->create(['author_id' => $author->id]);

        $response = $this->get(route('front.catalogue'));

        $response->assertStatus(200);
        $response->assertViewIs('front.catalogue');
    }

    public function test_test_cover_is_rendered_in_catalogue_and_book_details(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create([
            'author_id' => $author->id,
            'cover_path' => 'images/covers-test/example.svg',
        ]);

        $this->get(route('front.catalogue'))
            ->assertOk()
            ->assertSee($book->cover_url, false);

        $this->get(route('front.livres.show', $book->frontRouteParams()))
            ->assertOk()
            ->assertSee($book->cover_url, false);
    }

    public function test_catalogue_page_with_filter_works(): void
    {
        $author = Author::factory()->create();
        Book::factory()->create(['author_id' => $author->id, 'is_bestseller' => true]);
        Book::factory()->create(['author_id' => $author->id, 'is_bestseller' => false]);

        $response = $this->get(route('front.catalogue', ['filter' => 'bestseller']));

        $response->assertStatus(200);
        $response->assertViewIs('front.catalogue');
    }

    public function test_stories_index_works(): void
    {
        ShortStory::create([
            'titre' => 'Test Story',
            'extrait' => 'Test extract',
            'auteur' => 'Test Author',
        ]);

        $response = $this->get(route('front.stories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('front.stories.index');
    }

    public function test_contact_form_sends_email(): void
    {
        Mail::fake();

        $formData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'subject' => 'Demande d\'édition',
            'message' => 'Bonjour, j\'aimerais soumettre un manuscrit.',
        ];

        $response = $this->post(route('front.contact.send'), $formData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(ContactMessage::class, function ($mail) use ($formData) {
            return $mail->hasTo(config('mail.from.address')) &&
                   $mail->data['name'] === $formData['name'] &&
                   $mail->data['email'] === $formData['email'] &&
                   $mail->data['subject'] === $formData['subject'] &&
                   $mail->data['message'] === $formData['message'];
        });

        Mail::assertSent(ContactConfirmation::class, function ($mail) use ($formData) {
            return $mail->hasTo($formData['email']) &&
                   $mail->data['name'] === $formData['name'] &&
                   $mail->data['email'] === $formData['email'] &&
                   $mail->data['subject'] === $formData['subject'] &&
                   $mail->data['message'] === $formData['message'];
        });
    }

    public function test_processus_editions_page_works(): void
    {
        $response = $this->get(route('front.contrat.editions'));

        $response->assertStatus(200);
        $response->assertViewIs('front.contrat-editions');
    }
}
