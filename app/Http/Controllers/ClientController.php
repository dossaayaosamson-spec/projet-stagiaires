<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\CandidatureEcla;
use App\Services\Payments\FedaPayPaymentService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function __construct(
        private readonly FedaPayPaymentService $paymentService,
        private readonly SubscriptionService $subscriptionService
    ) {}

    /**
     * Bibliothèque : livres achetés par l'utilisateur
     */
    public function library()
    {
        $user = Auth::user();

        // Récupère les livres achetés via les commandes payées
        $livresAchetes = Book::whereHas('orderItems', function ($query) use ($user) {
            $query->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereIn('statut', ['confirmée', 'livrée']);
            });
        })->with('author')->get();

        return view('client.library', compact('livresAchetes'));
    }

    /**
     * Lectures en cours
     */
    public function readingInProgress()
    {
        $books = Auth::user()
            ->booksInProgress()
            ->with('author')
            ->get();

        return view('client.reading', compact('books'));
    }

    /**
     * Favoris
     */
    public function favorites()
    {
        $favorites = Auth::user()
            ->livresFavoris()
            ->latest('favoris.created_at')
            ->get();

        return view('client.favorites', compact('favorites'));
    }

    /**
     * Commandes
     */
    public function orders()
    {
        $orders = Auth::user()
            ->orders()
            ->with('items.book')
            ->latest()
            ->get();

        return view('client.orders', compact('orders'));
    }

    /**
     * Abonnement
     */
    public function subscription()
    {
        $subscription = Auth::user()->activeSubscription;

        return view('client.subscription', compact('subscription'));
    }

    /**
     * Souscrire à un plan d'abonnement
     * Affiche une page de confirmation
     */
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'plan' => 'required|string',
        ]);

        $plans = collect(config('plans'));
        $selectedPlan = $plans->firstWhere('nom', $data['plan']);

        if (! $selectedPlan) {
            return back()->with('error', 'Plan d\'abonnement invalide.');
        }

        // Afficher la page de confirmation avec les détails du plan
        return view('client.subscription-confirm', compact('selectedPlan'));
    }

    /**
     * Confirmer et créer l'abonnement
     */
    public function confirmSubscription(Request $request)
    {
        $data = $request->validate([
            'plan' => 'required|string',
        ]);

        $plans = collect(config('plans'));
        $selectedPlan = $plans->firstWhere('nom', $data['plan']);

        if (! $selectedPlan) {
            return back()->with('error', 'Plan d\'abonnement invalide.');
        }

        if ($selectedPlan['prix'] <= 0) {
            // Plan gratuit (si applicable)
            $this->subscriptionService->activateSubscription(Auth::user(), $selectedPlan);

            return redirect()->route('client.subscription')->with('success', 'Abonnement activé : '.$selectedPlan['nom'].'.');
        }

        try {
            $redirectUrl = route('subscription.return', ['plan' => $selectedPlan['nom']], true);

            $transaction = $this->paymentService->createSubscriptionTransaction(
                Auth::user(),
                $selectedPlan['nom'],
                $selectedPlan['prix'],
                $redirectUrl
            );

            $token = $transaction->generateToken();

            return redirect($token->url);
        } catch (\Exception $e) {
            Log::error('Erreur initialisation paiement abonnement: '.$e->getMessage());

            return back()->with('error', 'Une erreur est survenue lors de l\'initialisation du paiement. Veuillez réessayer.');
        }
    }

    /**
     * Profil
     */
    public function profile()
    {
        $user = Auth::user();

        return view('client.profile', compact('user'));
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'prenom' => ['required', 'string', 'max:255'],
            'nom_famille' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'telephone' => ['required', 'string'],
        ]);

        $user->update([
            'prenom' => $request->prenom,
            'nom_famille' => $request->nom_famille,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        return redirect()->route('client.profile')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Mes candidatures eCLA
     */
    public function candidatures()
    {
        $candidatures = CandidatureEcla::where('email', Auth::user()->email)
            ->latest()
            ->get();

        return view('client.candidatures', compact('candidatures'));
    }
}
