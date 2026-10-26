<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    /**
     * Affiche le tableau de bord de l'auteur.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // On récupère le profil auteur lié au user via la relation
        $author = $user->author;

        if (! $author) {
            // Créer un profil par défaut si inexistant (sécurité)
            $author = Author::create([
                'user_id' => $user->id,
                'nom' => $user->prenom.' '.$user->nom_famille,
                'initiales' => strtoupper(substr($user->prenom, 0, 1).substr($user->nom_famille, 0, 1)),
            ]);
        }

        // Récupérer les livres de l'auteur
        $authorBooks = $author->books()->latest()->get();
        $bookIds = $authorBooks->pluck('id');

        // Statistiques réelles
        // total_sales : Revenu total généré par les ventes confirmées
        $totalSales = OrderItem::whereIn('book_id', $bookIds)
            ->whereHas('order', function ($query) {
                $query->whereIn('statut', ['confirmée', 'livrée']);
            })
            ->sum(\DB::raw('quantite * prix_unitaire'));

        // total_readers : Nombre de lecteurs uniques ayant acheté au moins un ouvrage
        $totalReaders = Order::whereIn('statut', ['confirmée', 'livrée'])
            ->whereHas('items', function ($query) use ($bookIds) {
                $query->whereIn('book_id', $bookIds);
            })
            ->distinct('user_id')
            ->count('user_id');

        $stats = [
            'total_books' => $authorBooks->count(),
            'total_sales' => number_format($totalSales, 0, ',', ' '),
            'total_readers' => $totalReaders,
            'total_gagne' => number_format($author->total_gagne, 0, ',', ' '),
            'solde_disponible' => number_format($author->solde_disponible, 0, ',', ' '),
            'total_retire' => number_format($author->total_retire, 0, ',', ' '),
            'raw_solde' => (float) $author->solde_disponible,
        ];

        $manuscripts = $user->manuscripts()->latest('submitted_at')->get();
        $contracts = $author->contracts()->with('book')->latest('date_debut')->get();
        $payoutRequests = $author->payoutRequests()->latest()->get();

        return view('author.dashboard', compact('author', 'authorBooks', 'stats', 'manuscripts', 'contracts', 'payoutRequests'));
    }

    /**
     * Soumission d'une demande de retrait par l'auteur
     */
    public function storePayoutRequest(Request $request)
    {
        $user = Auth::user();
        $author = $user->author;

        if (! $author) {
            return redirect()->back()->with('error', 'Profil auteur introuvable.');
        }

        $minThreshold = 10000;

        $request->validate([
            'montant' => "required|numeric|min:{$minThreshold}|max:{$author->solde_disponible}",
            'moyen_paiement' => 'required|string|in:tmoney,flooz,virement_bancaire',
            'coordonnees_paiement' => 'required|string|max:500',
        ], [
            'montant.min' => 'Le seuil minimum de retrait est de '.number_format($minThreshold, 0, ',', ' ').' FCFA.',
            'montant.max' => 'Le montant demandé dépasse votre solde disponible ('.number_format($author->solde_disponible, 0, ',', ' ').' FCFA).',
        ]);

        // Vérifier s'il a déjà une demande en attente
        $hasPending = $author->payoutRequests()->where('statut', 'en_attente')->exists();
        if ($hasPending) {
            return redirect()->back()->with('error', 'Vous avez déjà une demande de versement en cours de traitement par notre équipe.');
        }

        PayoutRequest::create([
            'author_id' => $author->id,
            'montant' => $request->montant,
            'moyen_paiement' => $request->moyen_paiement,
            'coordonnees_paiement' => $request->coordonnees_paiement,
            'statut' => 'en_attente',
        ]);

        return redirect()->route('author.dashboard')->with('success', 'Votre demande de versement a été transmise avec succès au service financier.');
    }

    /**
     * Affiche le profil de l'auteur pour modification.
     */
    public function profile()
    {
        $author = Author::where('user_id', Auth::id())->firstOrFail();

        return view('author.profile', compact('author'));
    }

    /**
     * Met à jour le profil de l'auteur.
     */
    public function updateProfile(Request $request)
    {
        $author = Author::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'nom' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['nom', 'bio']);

        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar s'il existe
            if ($author->avatar && Storage::disk('public')->exists($author->avatar)) {
                Storage::disk('public')->delete($author->avatar);
            }
            $path = $request->file('avatar')->store('authors/avatars', 'public');
            $data['avatar'] = $path;
        }

        $author->update($data);

        return redirect()->route('author.profile')->with('success', 'Profil mis à jour avec succès.');
    }
}
