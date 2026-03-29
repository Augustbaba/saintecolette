<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Usage dans les routes :
     *
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,censeur')          ← OU logique
     *   ->middleware('role:admin|censeur')          ← syntaxe alternative
     *
     * Un utilisateur inactif est toujours refusé.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Non connecté
        if (! $user) {
            return redirect()->route('login');
        }

        // Compte désactivé
        if (! $user->actif) {
            auth()->logout();
            return redirect()->route('login')
                             ->withErrors(['email' => 'Votre compte a été désactivé.']);
        }

        // Vérification du rôle
        // Les rôles peuvent être passés comme 'admin,censeur' ou plusieurs arguments
        $allowed = collect($roles)
            ->flatMap(fn($r) => explode(',', $r))
            ->map(fn($r) => trim($r))
            ->filter()
            ->all();

        if (in_array($user->role, $allowed)) {
            return $next($request);
        }

        // Accès refusé → retour adapté selon le type de requête
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        abort(403, 'Vous n\'avez pas accès à cette section.');
    }
}
