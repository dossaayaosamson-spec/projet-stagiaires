<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Event;
use App\Models\ShortStory;
use App\Models\Slide;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function welcome()
    {
        // Durée de cache : 24 heures (1440 minutes)
        $cacheDuration = 1440;

        $slides = Cache::remember('welcome_slides', 60, function () {
            return Slide::where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->orderBy('order')
                ->get();
        });

        $events = Cache::remember('welcome_events', $cacheDuration, function () {
            return Event::upcoming()->take(6)->get(); // Limiter à 6 événements
        });

        $bestSellers = Cache::remember('welcome_bestsellers', $cacheDuration, function () {
            return Book::with('author')
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->where('is_bestseller', true)
                ->take(4)
                ->get();
        });

        $latestBooks = Book::with('author')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->latest()
            ->take(5)
            ->get();
        $userRatings = Auth::check()
            ? Auth::user()->bookRatings()->whereIn('book_id', $latestBooks->modelKeys())->get()->keyBy('book_id')
            : collect();

        $authors = Cache::remember('welcome_authors', $cacheDuration, function () {
            return Author::withCount('books')->take(3)->get(); // Limiter à 3 pour l'affichage (md:grid-cols-3)
        });

        $stories = Cache::remember('welcome_stories', $cacheDuration, function () {
            return ShortStory::take(3)->get(); // Limiter à 3 pour l'affichage (md:grid-cols-3)
        });

        $booksCount = Cache::remember('welcome_books_count', $cacheDuration, function () {
            return Book::count();
        });

        $readersCount = Cache::remember('welcome_readers_count', $cacheDuration, function () {
            return User::count();
        });

        $categoriesCount = Cache::remember('welcome_categories_count', $cacheDuration, function () {
            return Book::distinct('categorie')->count();
        });

        // On prend un livre au hasard, mis en cache pour 24h (Livre du jour)
        $featuredBook = Cache::remember('welcome_featured_book', $cacheDuration, function () {
            return Book::with('author')->inRandomOrder()->first();
        });

        // Plans d'abonnement (données statiques du seeder)
        $plans = config('plans');

        return view('welcome', [
            'slides' => $slides,
            'events' => $events,
            'books' => collect(), // Rétrocompatibilité si d'autres vues l'utilisaient de cette manière, mais on va plutôt modifier la vue !
            'bestSellers' => $bestSellers,
            'latestBooks' => $latestBooks,
            'userRatings' => $userRatings,
            'authors' => $authors,
            'stories' => $stories,
            'featuredBook' => $featuredBook,
            'plans' => $plans,
            'booksCount' => $booksCount,
            'readersCount' => $readersCount,
            'categoriesCount' => $categoriesCount,
        ]);
    }
}
