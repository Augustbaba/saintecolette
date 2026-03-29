<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EnseignantController extends Controller
{
    public function index()
    {
        $enseignants = Enseignant::with('user')
            ->orderBy('nom')
            ->paginate(15);

        return view('back.pages.enseignants.index', compact('enseignants'));
    }

    public function create()
    {
        return view('back.pages.enseignants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'               => 'required|string|max:50',
            'prenom'            => 'required|string|max:50',
            'email'             => 'required|email|unique:users,email',
            'telephone'         => 'nullable|string|max:20',
            'matiere_principale'=> 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['prenom'] . ' ' . $validated['nom'],
                'email'    => $validated['email'],
                'password' => Hash::make('password123'),
                'role'     => 'enseignant',
                'actif'    => true,
            ]);

            Enseignant::create([
                'user_id'            => $user->id,
                'nom'                => $validated['nom'],
                'prenom'             => $validated['prenom'],
                'telephone'          => $validated['telephone'] ?? null,
                'matiere_principale' => $validated['matiere_principale'] ?? null,
            ]);
        });

        return redirect()->route('admin.enseignants.index')
                         ->with('success', 'Enseignant créé avec succès.');
    }

    public function show(Enseignant $enseignant)
    {
        $enseignant->load(['user', 'seances.matiere', 'seances.classeAnnee.classe.niveau', 'seances.classeAnnee.anneeScolaire']);

        // Organiser les séances par jour
        $seancesParJour = $enseignant->seances->groupBy('jour_semaine')->sortKeys();

        return view('back.pages.enseignants.show', compact('enseignant', 'seancesParJour'));
    }

    public function edit(Enseignant $enseignant)
    {
        $enseignant->load('user');
        return view('back.pages.enseignants.edit', compact('enseignant'));
    }

    public function update(Request $request, Enseignant $enseignant)
    {
        $validated = $request->validate([
            'nom'               => 'required|string|max:50',
            'prenom'            => 'required|string|max:50',
            'email'             => 'required|email|unique:users,email,' . $enseignant->user_id,
            'telephone'         => 'nullable|string|max:20',
            'matiere_principale'=> 'nullable|string|max:100',
            'actif'             => 'boolean',
        ]);

        DB::transaction(function () use ($validated, $enseignant, $request) {
            $enseignant->user->update([
                'name'  => $validated['prenom'] . ' ' . $validated['nom'],
                'email' => $validated['email'],
                'actif' => $request->boolean('actif'),
            ]);

            $enseignant->update([
                'nom'                => $validated['nom'],
                'prenom'             => $validated['prenom'],
                'telephone'          => $validated['telephone'] ?? null,
                'matiere_principale' => $validated['matiere_principale'] ?? null,
            ]);
        });

        return redirect()->route('admin.enseignants.index')
                         ->with('success', 'Enseignant mis à jour avec succès.');
    }

    public function destroy(Enseignant $enseignant)
    {
        // Supprimer le user associé (cascade supprime l'enseignant)
        $enseignant->user->delete();

        return redirect()->route('admin.enseignants.index')
                         ->with('success', 'Enseignant supprimé avec succès.');
    }

    public function resetPassword(Enseignant $enseignant)
    {
        $enseignant->user->update([
            'password' => Hash::make('password123'),
        ]);

        return back()->with('success', 'Mot de passe réinitialisé à password123.');
    }
}
