<?php

namespace App\Http\Controllers;

use App\Mail\ContactConfirmation;
use App\Mail\ContactMessage;
use App\Models\Author;
use App\Models\Book;
use App\Models\Event;
use App\Models\ShortStory;
use App\Models\ShortStoryPurchase;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class FrontController extends Controller
{
    public function catalogue(Request $request)
    {
        $query = Book::with('author')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->latest();

        if ($request->has('filter') && $request->input('filter') === 'bestseller') {
            $query->where('is_bestseller', true);
        }

        $books = $query->paginate(12);
        $userRatings = Auth::check()
            ? Auth::user()->bookRatings()->whereIn('book_id', $books->getCollection()->modelKeys())->get()->keyBy('book_id')
            : collect();

        return view('front.catalogue', compact('books', 'userRatings'));
    }

    public function showBook($id)
    {
        $book = Book::with('author')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->findOrFail($id);
        $userRating = Auth::check()
            ? Auth::user()->bookRatings()->where('book_id', $book->id)->first()
            : null;

        return view('front.books.show', compact('book', 'userRating'));
    }

    public function authorsIndex()
    {
        $authors = Cache::remember('front_authors_index', 1440, function () {
            return Author::withCount('books')->orderBy('nom')->get();
        });

        return view('front.authors.index', compact('authors'));
    }

    public function showAuthor($id)
    {
        $author = Author::findOrFail($id);
        $books = Book::query()->where('author_id', $id)->get();

        return view('front.authors.show', compact('author', 'books'));
    }

    public function storiesIndex()
    {
        $page = request()->input('page', 1);
        $stories = Cache::remember('front_stories_page_'.$page, 1440, function () {
            return ShortStory::query()->latest()->paginate(12);
        });

        return view('front.stories.index', compact('stories'));
    }

    public function showStory($id, SubscriptionService $subscriptionService)
    {
        $story = ShortStory::findOrFail($id);

        /** @var User|null $user */
        $user = Auth::user();
        $hasAccess = false;

        if ($story->is_free) {
            $hasAccess = true;
        } elseif ($user) {
            if ($user->role === 'admin') {
                $hasAccess = true;
            } elseif ($user->author && $story->author_id === $user->author->id) {
                $hasAccess = true;
            } else {
                if ($subscriptionService->hasActiveSubscription($user)) {
                    $hasAccess = true;
                } else {
                    $hasPurchased = ShortStoryPurchase::query()->where('user_id', $user->id)
                        ->where('short_story_id', $story->id)
                        ->where('status', 'successful')
                        ->exists();
                    if ($hasPurchased) {
                        $hasAccess = true;
                    }
                }
            }
        }

        if (! $hasAccess) {
            // Troncate to 30%, capped at 1000 characters
            $length = mb_strlen((string) $story->extrait);
            $cutPoint = min((int) ($length * 0.30), 1000);
            $story->extrait = mb_substr((string) $story->extrait, 0, $cutPoint).'...';
        }

        return view('front.stories.show', compact('story', 'hasAccess'));
    }

    public function showEvent($id)
    {
        $event = Event::findOrFail($id);

        return view('front.events.show', compact('event'));
    }

    public function contact()
    {
        return view('front.contact');
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Mail::to(config('mail.from.address'))->send(new ContactMessage($validated));
        Mail::to($validated['email'])->send(new ContactConfirmation($validated));

        return redirect()->back()->with('success', 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.');
    }

    public function plans()
    {
        $plans = config('plans');

        return view('front.plans', compact('plans'));
    }

    public function contratEditions()
    {
        return view('front.contrat-editions');
    }
}
