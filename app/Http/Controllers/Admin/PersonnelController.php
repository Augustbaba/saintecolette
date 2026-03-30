<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PersonnelController extends Controller
{
    // Rôles gérés par ce controller (hors admin et enseignant qui ont leurs propres CRUDs)
    const ROLES_PERSONNEL = [
        'censeur'     => 'Censeur',
        'secretaire'  => 'Secrétaire',
        'comptable'   => 'Comptable',
        'surveillant' => 'Surveillant',
    ];

    public function index(Request $request)
    {
        $query = User::whereIn('role', array_keys(self::ROLES_PERSONNEL))
                     ->orderBy('name');

        // Filtre par rôle
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtre par recherche
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name',  'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $personnel = $query->paginate(15)->withQueryString();

        return view('back.pages.personnel.index', [
            'personnel'      => $personnel,
            'rolesPersonnel' => self::ROLES_PERSONNEL,
        ]);
    }

    public function create()
    {
        return view('back.pages.personnel.create', [
            'rolesPersonnel' => self::ROLES_PERSONNEL,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role'  => ['required', Rule::in(array_keys(self::ROLES_PERSONNEL))],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make('password123'),
            'role'     => $validated['role'],
            'actif'    => true,
        ]);

        return redirect()->route('admin.personnel.index')
                         ->with('success', 'Membre du personnel créé.');
    }

    public function edit(User $personnel)
    {
        // Sécurité : on ne peut pas éditer un admin ou enseignant via ce controller
        abort_if(
            ! in_array($personnel->role, array_keys(self::ROLES_PERSONNEL)),
            403,
            'Ce compte n\'est pas géré ici.'
        );

        return view('back.pages.personnel.edit', [
            'membre'         => $personnel,
            'rolesPersonnel' => self::ROLES_PERSONNEL,
        ]);
    }

    public function update(Request $request, User $personnel)
    {
        abort_if(
            ! in_array($personnel->role, array_keys(self::ROLES_PERSONNEL)),
            403
        );

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($personnel->id)],
            'role'  => ['required', Rule::in(array_keys(self::ROLES_PERSONNEL))],
            'actif' => 'boolean',
        ]);

        $personnel->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
            'actif' => $request->boolean('actif'),
        ]);

        return redirect()->route('admin.personnel.index')
                         ->with('success', 'Membre du personnel mis à jour.');
    }

    public function destroy(User $personnel)
    {
        abort_if(
            ! in_array($personnel->role, array_keys(self::ROLES_PERSONNEL)),
            403
        );

        $personnel->delete();

        return redirect()->route('admin.personnel.index')
                         ->with('success', 'Membre du personnel supprimé.');
    }

    public function resetPassword(User $personnel)
    {
        abort_if(
            ! in_array($personnel->role, array_keys(self::ROLES_PERSONNEL)),
            403
        );

        $personnel->update([
            'password' => Hash::make('password123'),
        ]);

        return back()->with('success', 'Mot de passe réinitialisé à password123.');
    }
}
