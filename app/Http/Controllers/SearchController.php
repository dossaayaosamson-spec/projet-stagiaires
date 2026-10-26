<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\ShortStory;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (! $query) {
            return redirect()->route('home');
        }

        $books = Book::where('titre', 'like', "%{$query}%")
            ->orWhere('resume', 'like', "%{$query}%")
            ->orWhere('categorie', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        $authors = Author::where('nom', 'like', "%{$query}%")
            ->orWhere('bio', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        $stories = ShortStory::where('titre', 'like', "%{$query}%")
            ->orWhere('extrait', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return view('front.search', compact('books', 'authors', 'stories', 'query'));
    }
}
