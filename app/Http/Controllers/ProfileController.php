<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Affiche la page profil.
     */
    public function edit()
    {
        return view('back.pages.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Met à jour name + email.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return back()->with('success_profil', 'Profil mis à jour avec succès.');
    }

    /**
     * Met à jour le mot de passe.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required|string',
            'password'                  => ['required', 'confirmed', Password::min(8)],
            'password_confirmation'     => 'required',
        ]);

        $user = Auth::user();

        // Vérifier que l'ancien mot de passe est correct
        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                ->withFragment('password'); // scroll vers la section mdp
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()
            ->with('success_password', 'Mot de passe modifié avec succès.')
            ->withFragment('password');
    }
}
