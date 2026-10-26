<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspended
{
    /**
     * Bloque les utilisateurs suspendus pour toute action d'écriture.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isSuspended()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Votre compte est suspendu. Vous ne pouvez pas effectuer cette action.',
                ], 403);
            }

            return redirect()->back()->withErrors([
                'suspension' => 'Votre compte est actuellement suspendu. Vous ne pouvez pas publier de commentaires.',
            ]);
        }

        return $next($request);
    }
}
