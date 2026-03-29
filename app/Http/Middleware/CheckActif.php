<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware séparé pour vérifier uniquement le statut actif/inactif.
 * Utile pour protéger toutes les routes authentifiées sans vérifier de rôle.
 *
 * Usage : ->middleware('actif')
 */
class CheckActif
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->actif) {
            auth()->logout();
            return redirect()->route('login')
                             ->withErrors(['email' => 'Votre compte a été désactivé. Contactez l\'administrateur.']);
        }

        return $next($request);
    }
}
