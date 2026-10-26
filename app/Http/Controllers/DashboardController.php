<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirection selon le rôle
        $role = strtolower($user->role);

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (in_array($role, ['auteur', 'author'], true)) {
            return redirect()->route('author.dashboard');
        }

        // Livres en cours de lecture
        $inProgressBooks = $user->booksInProgress()->with('author')->get();
        $latestReadBook = $user->latestReadBook();

        // Recommandation : un livre que l'utilisateur n'a pas encore lu ou commencé
        $recommendedBook = Book::whereDoesntHave('readers', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with('author')->inRandomOrder()->first();

        // Stats
        $stats = [
            'in_progress' => $inProgressBooks->count(),
            'finished' => $user->booksFinished()->count(),
            'favorites' => $user->livresFavoris()->count(),
            'reading_time' => $inProgressBooks->count() * 45 .' min', // estimation simple
        ];

        // Dernières commandes
        $recentOrders = $user->orders()->latest()->take(5)->get();

        // Abonnement actif
        $activeSubscription = $user->activeSubscription;

        return view('client.dashboard', compact(
            'inProgressBooks',
            'latestReadBook',
            'recommendedBook',
            'stats',
            'recentOrders',
            'activeSubscription'
        ));
    }
}
