@extends('back.layouts.master')

@section('content')
<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Modifier — {{ $enseignant->nom_complet }}</h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ <a href="{{ route('admin.enseignants.index') }}" class="text-secondary-light hover-text-primary hover-underline">Enseignants</a></span>
                <span class="text-secondary-light">/ Modifier</span>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ri-edit-line me-2 text-primary-600"></i>Informations de l'enseignant</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.enseignants.update', $enseignant) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="prenom" class="form-label fw-medium">Prénom <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('prenom') is-invalid @enderror"
                                       id="prenom" name="prenom"
                                       value="{{ old('prenom', $enseignant->prenom) }}" required>
                                @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-sm-6">
                                <label for="nom" class="form-label fw-medium">Nom <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('nom') is-invalid @enderror"
                                       id="nom" name="nom"
                                       value="{{ old('nom', $enseignant->nom) }}" required>
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email', $enseignant->user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-sm-6">
                                <label for="telephone" class="form-label fw-medium">Téléphone</label>
                                <input type="text"
                                       class="form-control @error('telephone') is-invalid @enderror"
                                       id="telephone" name="telephone"
                                       value="{{ old('telephone', $enseignant->telephone) }}">
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-sm-6">
                                <label for="matiere_principale" class="form-label fw-medium">Matière principale</label>
                                <input type="text"
                                       class="form-control @error('matiere_principale') is-invalid @enderror"
                                       id="matiere_principale" name="matiere_principale"
                                       value="{{ old('matiere_principale', $enseignant->matiere_principale) }}">
                                @error('matiere_principale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1"
                                           {{ old('actif', $enseignant->user->actif) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="actif">Compte actif</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary-600 flex-grow-1">
                                <i class="ri-save-line me-2"></i>Enregistrer les modifications
                            </button>
                            <a href="{{ route('admin.enseignants.index') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
