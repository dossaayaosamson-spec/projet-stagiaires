<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_aggregate_rating_but_must_sign_in_to_rate(): void
    {
        $book = $this->createBook();
        BookRating::create([
            'book_id' => $book->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 5,
        ]);
        BookRating::create([
            'book_id' => $book->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 3,
        ]);

        $this->get(route('front.livres.show', $book->frontRouteParams()))
            ->assertOk()
            ->assertSee('4,0/5', false)
            ->assertSee('2 notes')
            ->assertSee(route('login'));

        $this->post(route('front.ratings.store', $book), ['rating' => 4])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('book_ratings', 2);
    }

    public function test_home_and_catalogue_show_book_rating_summaries(): void
    {
        $book = $this->createBook();
        BookRating::create([
            'book_id' => $book->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 5,
        ]);
        BookRating::create([
            'book_id' => $book->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 3,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('4,0/5', false)
            ->assertSee('2 notes');

        $this->get(route('front.catalogue'))
            ->assertOk()
            ->assertSee('4,0/5', false)
            ->assertSee('2 notes');
    }

    public function test_unrated_books_show_no_statistic_and_prompt_guests_to_sign_in_when_opening_rating_options(): void
    {
        $this->createBook();

        $this->get(route('front.catalogue'))
            ->assertOk()
            ->assertSee('&#9734;', false)
            ->assertSee('Veuillez vous connecter pour pouvoir noter ce livre.')
            ->assertSee(route('login'))
            ->assertDontSee('Aucune note');

        $this->assertDatabaseCount('book_ratings', 0);
    }

    public function test_reader_can_rate_from_catalogue_and_see_the_updated_statistic_immediately(): void
    {
        $book = $this->createBook();
        $reader = User::factory()->create(['role' => 'client']);
        $catalogueUrl = route('front.catalogue');

        $this->actingAs($reader)
            ->get($catalogueUrl)
            ->assertOk()
            ->assertSee('action="'.route('front.ratings.store', $book->id).'"', false)
            ->assertSee('name="rating" value="5"', false)
            ->assertDontSee('Veuillez vous connecter pour pouvoir noter ce livre.');

        $this->actingAs($reader)
            ->post(route('front.ratings.store', $book), ['rating' => 4], ['Referer' => $catalogueUrl])
            ->assertRedirect($catalogueUrl);

        $this->assertDatabaseHas('book_ratings', [
            'book_id' => $book->id,
            'user_id' => $reader->id,
            'rating' => 4,
        ]);

        $this->get($catalogueUrl)
            ->assertOk()
            ->assertSee('4,0/5', false)
            ->assertSee('1 note')
            ->assertSee('name="_method" value="PUT"', false);
    }

    public function test_authenticated_reader_can_create_update_and_delete_their_rating(): void
    {
        $book = $this->createBook();
        $reader = User::factory()->create(['role' => 'client']);

        $this->actingAs($reader)
            ->post(route('front.ratings.store', $book), ['rating' => 3])
            ->assertRedirect();

        $this->assertDatabaseHas('book_ratings', [
            'book_id' => $book->id,
            'user_id' => $reader->id,
            'rating' => 3,
        ]);

        $this->put(route('front.ratings.update', $book), ['rating' => 5])
            ->assertRedirect();

        $this->assertDatabaseHas('book_ratings', [
            'book_id' => $book->id,
            'user_id' => $reader->id,
            'rating' => 5,
        ]);

        $this->delete(route('front.ratings.destroy', $book))
            ->assertRedirect();

        $this->assertDatabaseMissing('book_ratings', [
            'book_id' => $book->id,
            'user_id' => $reader->id,
        ]);
    }

    public function test_reader_cannot_create_a_second_rating_for_the_same_book(): void
    {
        $book = $this->createBook();
        $reader = User::factory()->create(['role' => 'client']);

        $this->actingAs($reader)
            ->post(route('front.ratings.store', $book), ['rating' => 4])
            ->assertRedirect();

        $this->post(route('front.ratings.store', $book), ['rating' => 2])
            ->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('book_ratings', 1);
        $this->assertDatabaseHas('book_ratings', [
            'book_id' => $book->id,
            'user_id' => $reader->id,
            'rating' => 4,
        ]);
    }

    public function test_rating_must_be_an_integer_between_one_and_five(): void
    {
        $book = $this->createBook();
        $reader = User::factory()->create(['role' => 'client']);
        $this->actingAs($reader);

        foreach ([0, 6, 'not-a-number'] as $rating) {
            $this->post(route('front.ratings.store', $book), ['rating' => $rating])
                ->assertSessionHasErrors('rating');
        }

        $this->assertDatabaseCount('book_ratings', 0);
    }

    public function test_reader_cannot_change_or_delete_another_readers_rating(): void
    {
        $book = $this->createBook();
        $owner = User::factory()->create(['role' => 'client']);
        $otherReader = User::factory()->create(['role' => 'client']);
        BookRating::create([
            'book_id' => $book->id,
            'user_id' => $owner->id,
            'rating' => 4,
        ]);

        $this->actingAs($otherReader)
            ->put(route('front.ratings.update', $book), ['rating' => 1])
            ->assertNotFound();

        $this->delete(route('front.ratings.destroy', $book))
            ->assertNotFound();

        $this->assertDatabaseHas('book_ratings', [
            'book_id' => $book->id,
            'user_id' => $owner->id,
            'rating' => 4,
        ]);
    }

    private function createBook(): Book
    {
        return Book::factory()->create([
            'author_id' => Author::factory()->create()->id,
        ]);
    }
}
