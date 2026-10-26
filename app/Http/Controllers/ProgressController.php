<?php

namespace App\Http\Controllers;

use App\Models\BookUserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * Mettre à jour la progression de lecture d'un livre
     * Appelé automatiquement depuis le lecteur PDF
     */
    public function update(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $user = Auth::user();
        $bookId = $request->input('book_id');
        $percent = (int) $request->input('progress');

        // Récupérer ou créer l'entrée de progression
        $progress = BookUserProgress::firstOrNew([
            'user_id' => $user->id,
            'book_id' => $bookId,
        ]);

        $progress->updateProgress($percent);

        return response()->json([
            'success' => true,
            'progress' => $progress->progress,
            'status' => $progress->status,
            'message' => $progress->status === 'finished'
                ? 'Félicitations, vous avez terminé ce livre !'
                : "Progression enregistrée : {$progress->progress}%",
        ]);
    }

    /**
     * Récupérer la progression d'un livre pour l'utilisateur connecté
     */
    public function get(int $bookId)
    {
        $progress = BookUserProgress::where(['user_id' => Auth::id(), 'book_id' => $bookId])->first();

        return response()->json([
            'progress' => $progress ? $progress->progress : 0,
            'status' => $progress ? $progress->status : 'reading',
        ]);
    }
}
