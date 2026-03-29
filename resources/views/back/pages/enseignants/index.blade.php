@extends('back.layouts.master')

@section('content')
<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des enseignants</h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Enseignants</span>
            </div>
        </div>
        <a href="{{ route('admin.enseignants.create') }}" class="btn btn-primary-600 d-flex align-items-center gap-6">
            <i class="ri-add-line"></i> Ajouter un enseignant
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-24" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-24" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card h-100">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Matière principale</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enseignants as $enseignant)
                        <tr>
                            <td>{{ $enseignant->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;">
                                        {{ strtoupper(substr($enseignant->prenom, 0, 1)) }}{{ strtoupper(substr($enseignant->nom, 0, 1)) }}
                                    </div>
                                    <span class="fw-medium">{{ $enseignant->nom_complet }}</span>
                                </div>
                            </td>
                            <td>{{ $enseignant->user->email ?? '—' }}</td>
                            <td>{{ $enseignant->telephone ?? '—' }}</td>
                            <td>{{ $enseignant->matiere_principale ?? '—' }}</td>
                            <td>
                                @if($enseignant->user?->actif)
                                    <span class="badge text-sm fw-semibold bg-success-100 text-success-600 px-20 py-9 radius-4">Actif</span>
                                @else
                                    <span class="badge text-sm fw-semibold bg-danger-100 text-danger-600 px-20 py-9 radius-4">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a href="{{ route('admin.enseignants.show', $enseignant) }}"
                                       class="btn btn-sm btn-info-100 text-info-600"
                                       title="Voir l'emploi du temps">
                                        <i class="ri-calendar-line"></i>
                                    </a>
                                    <a href="{{ route('admin.enseignants.edit', $enseignant) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Modifier">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.enseignants.reset-password', $enseignant) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Réinitialiser le mot de passe à password123 ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Reset mdp">
                                            <i class="ri-lock-password-line"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.enseignants.destroy', $enseignant) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer cet enseignant ? Toutes ses séances seront supprimées.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary-light">
                                <i class="ri-user-line ri-2x mb-2 d-block"></i>
                                Aucun enseignant enregistré.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($enseignants->hasPages())
                <div class="p-3 border-top">
                    {{ $enseignants->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
