@extends('back.layouts.master')

@section('content')
<div class="dashboard-main-body">

    {{-- Breadcrumb --}}
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Mon profil</h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Mon profil</span>
            </div>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">

            {{-- ══════════════════════════════════════════
                 SECTION 1 — Informations du profil
            ══════════════════════════════════════════ --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-3">
                    {{-- Avatar initiales --}}
                    <div class="rounded-circle bg-primary-100 text-primary-600 d-flex align-items-center justify-content-center fw-bold"
                         style="width:48px;height:48px;min-width:48px;font-size:18px;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="mb-0 fw-semibold">{{ Auth::user()->name }}</h5>
                        <span class="text-secondary-light" style="font-size:13px;">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Alerte succès profil --}}
                    @if(session('success_profil'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success_profil') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">
                                Nom complet <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name"
                                   value="{{ old('name', Auth::user()->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">
                                Adresse email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email"
                                   value="{{ old('email', Auth::user()->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary-600">
                                <i class="ri-save-line me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 SECTION 2 — Modifier le mot de passe
            ══════════════════════════════════════════ --}}
            <div class="card" id="password">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-lock-password-line me-2 text-warning-600"></i>
                        Modifier le mot de passe
                    </h5>
                </div>

                <div class="card-body">
                    {{-- Alerte succès mot de passe --}}
                    @if(session('success_password'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success_password') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-medium">
                                Mot de passe actuel <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       name="current_password"
                                       placeholder="Votre mot de passe actuel"
                                       autocomplete="current-password">
                                <button class="btn btn-outline-secondary toggle-pwd" type="button"
                                        data-target="current_password">
                                    <i class="ri-eye-line"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">
                                Nouveau mot de passe <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       placeholder="Minimum 8 caractères"
                                       autocomplete="new-password">
                                <button class="btn btn-outline-secondary toggle-pwd" type="button"
                                        data-target="password">
                                    <i class="ri-eye-line"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-medium">
                                Confirmer le nouveau mot de passe <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       name="password_confirmation"
                                       placeholder="Répétez le nouveau mot de passe"
                                       autocomplete="new-password">
                                <button class="btn btn-outline-secondary toggle-pwd" type="button"
                                        data-target="password_confirmation">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning-600">
                                <i class="ri-lock-password-line me-2"></i>Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


