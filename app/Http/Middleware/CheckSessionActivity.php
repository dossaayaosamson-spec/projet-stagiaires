<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionActivity
{
    /**
     * Gère l'expiration dynamique de session selon le rôle de l'utilisateur.
     * - 5 minutes (300 s) pour Auteur et Admin
     * - 10 minutes (600 s) pour Lecteur (client / autres)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $maxInactiveSeconds = match ($user->role) {
                'admin', 'author' => 300, // 5 minutes
                default => 600,            // 10 minutes
            };

            $lastActivity = session('last_activity_timestamp');

            if ($lastActivity !== null && (time() - $lastActivity) > $maxInactiveSeconds) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $minutes = (int) ($maxInactiveSeconds / 60);

                return redirect()->route('login')->with('error', "Votre session a expiré après {$minutes} minutes d'inactivité.");
            }

            session(['last_activity_timestamp' => time()]);
        }

        return $next($request);
    }
}
