<?php

namespace App\Http\Controllers;

use App\Models\Favori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriController extends Controller
{
    /**
     * Afficher la page des favoris du lecteur connecté
     */
    public function index()
    {
        $favoris = Auth::user()
            ->livresFavoris()
            ->latest('favoris.created_at')
            ->get();

        return view('favoris.index', compact('favoris'));
    }

    /**
     * Ajouter un livre aux favoris
     */
    public function store(Request $request)
    {
        $request->validate([
            'livre_id' => 'required|exists:books,id', // table "books"
        ]);

        $user = Auth::user();

        // Vérifier si déjà en favori
        if ($user->aEnFavori($request->livre_id)) {
            return back()->with('info', 'Ce livre est déjà dans vos favoris.');
        }

        Favori::create([
            'user_id' => $user->id,
            'livre_id' => $request->livre_id,
        ]);

        return back()->with('success', 'Livre ajouté à vos favoris.');
    }

    /**
     * Retirer un livre des favoris
     */
    public function destroy(int $livreId)
    {
        $supprime = Auth::user()
            ->favoris()
            ->where('livre_id', $livreId)
            ->delete();

        return back()->with(
            $supprime ? 'success' : 'error',
            $supprime
                ? 'Livre retiré de vos favoris.'
                : 'Ce livre n\'est pas dans vos favoris.'
        );
    }

    /**
     * Basculer favori (toggle)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'livre_id' => 'required|exists:books,id',
        ]);

        $user = Auth::user();
        $livreId = $request->livre_id;

        $favori = $user->favoris()->where('livre_id', $livreId)->first();

        if ($favori) {
            $favori->delete();
            $estFavori = false;
            $message = 'Retiré des favoris.';
        } else {
            Favori::create([
                'user_id' => $user->id,
                'livre_id' => $livreId,
            ]);
            $estFavori = true;
            $message = 'Ajouté aux favoris.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'est_favori' => $estFavori,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
