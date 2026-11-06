<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookRatingController extends Controller
{
    public function store(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        $rating = $request->user()->bookRatings()->firstOrCreate(
            ['book_id' => $book->id],
            ['rating' => $validated['rating']],
        );

        if (! $rating->wasRecentlyCreated) {
            return back()->withErrors([
                'rating' => 'Vous avez déjà noté ce livre. Modifiez votre note existante.',
            ])->withInput();
        }

        return back()
            ->with('success', 'Votre note a bien été enregistrée.');
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        $rating = $request->user()->bookRatings()
            ->where('book_id', $book->id)
            ->firstOrFail();

        $rating->update(['rating' => $validated['rating']]);

        return back()
            ->with('success', 'Votre note a bien été modifiée.');
    }

    public function destroy(Request $request, Book $book): RedirectResponse
    {
        $request->user()->bookRatings()
            ->where('book_id', $book->id)
            ->firstOrFail()
            ->delete();

        return back()
            ->with('success', 'Votre note a bien été supprimée.');
    }
}
