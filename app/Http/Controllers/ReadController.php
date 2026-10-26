<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookUserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReadController extends Controller
{
    /**
     * Page du lecteur PDF
     */
    public function read(int $bookId)
    {
        $book = Book::with('author')->findOrFail($bookId);

        // Vérifier l'accès
        if (! $this->hasAccess($book)) {
            return redirect()->route('front.livres.show', $book->frontRouteParams())
                ->with('error', 'Vous devez acheter ce livre ou avoir un abonnement actif pour le lire.');
        }

        // Récupérer ou initialiser la progression
        $progress = null;
        if (Auth::check()) {
            $progress = BookUserProgress::firstOrCreate(
                ['user_id' => Auth::id(), 'book_id' => $bookId],
                ['progress' => 0, 'status' => 'reading', 'last_read_at' => now()]
            );
        }

        return view('front.books.reader', compact('book', 'progress'));
    }

    /**
     * Servir le PDF de façon sécurisée
     * Double vérification : auth + accès au livre
     */
    public function servePdf(Request $request, int $bookId)
    {
        $book = Book::findOrFail($bookId);

        // 1. Vérifier l'accès complet (gratuité, achat, abonnement)
        if (! $this->hasAccess($book)) {
            if (! Auth::check()) {
                abort(401, 'Vous devez être connecté pour accéder à ce livre.');
            }
            abort(403, 'Accès refusé. Vous n\'avez pas accès à ce livre.');
        }

        // 3. Vérifier que le fichier existe
        if (! $book->pdf_path) {
            abort(404, 'Aucun fichier PDF associé à ce livre.');
        }

        // 4. Le PDF doit être dans le disque LOCAL (pas public)
        if (! Storage::disk('local')->exists($book->pdf_path)) {
            abort(404, 'Fichier PDF introuvable sur le serveur.');
        }

        // 5. Servir le fichier de façon sécurisée
        $pdfContent = Storage::disk('local')->get($book->pdf_path);

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="lecture.pdf"')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('X-Content-Type-Options', 'nosniff');
    }

    /**
     * Vérifier si l'utilisateur a accès au livre
     */
    private function hasAccess(Book $book): bool
    {
        // Livre gratuit temporairement (tout le monde y a accès)
        if ($book->is_free) {
            // Gratuit à vie si free_until est null, sinon vérifier la date
            if ($book->free_until === null || $book->free_until->isFuture()) {
                return true;
            }
        }

        // Non connecté : pas d'accès
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // A acheté le livre via une commande payée
        $aAchete = $user->orders()
            ->whereIn('statut', ['paye', 'confirmée', 'livrée'])
            ->whereHas('items', fn ($q) => $q->where('book_id', $book->id))
            ->exists();

        if ($aAchete) {
            return true;
        }

        // A un abonnement actif
        if ($user->activeSubscription) {
            return true;
        }

        return false;
    }
}
