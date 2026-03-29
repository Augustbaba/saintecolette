@extends('back.layouts.master')

@php
    $jours = \App\Models\Seance::JOURS;
@endphp

@section('content')
<div class="dashboard-main-body">

    {{-- Breadcrumb --}}
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">
                {{ $classeAnnee->classe->full_name ?? '—' }}
                <span class="text-secondary-light fw-normal">— {{ $classeAnnee->anneeScolaire->libelle ?? '' }}</span>
            </h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ <a href="{{ route('admin.classe-annees.index') }}" class="text-secondary-light hover-text-primary hover-underline">Classes par année</a></span>
                <span class="text-secondary-light">/ Matières & Emploi du temps</span>
            </div>
        </div>
    </div>

    {{-- Alertes --}}
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

    <div class="row g-4">

        {{-- ── Colonne gauche : Ajouter matière ── --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ri-book-open-line me-2 text-primary-600"></i>Ajouter une matière</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.classe-matieres.store', $classeAnnee) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="matiere_id" class="form-label fw-medium">Matière <span class="text-danger">*</span></label>
                            <select name="matiere_id" id="matiere_id"
                                    class="form-select @error('matiere_id') is-invalid @enderror" required>
                                <option value="">— Choisir —</option>
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                        {{ $matiere->nom_matiere }}
                                    </option>
                                @endforeach
                            </select>
                            @error('matiere_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="enseignant_id" class="form-label fw-medium">Enseignant <span class="text-danger">*</span></label>
                            <select name="enseignant_id" id="enseignant_id"
                                    class="form-select @error('enseignant_id') is-invalid @enderror" required>
                                <option value="">— Choisir —</option>
                                @foreach($enseignants as $enseignant)
                                    <option value="{{ $enseignant->id }}" {{ old('enseignant_id') == $enseignant->id ? 'selected' : '' }}>
                                        {{ $enseignant->nom_complet }}
                                        @if ($enseignant->matiere_principale)
                                            <small>({{ $enseignant->matiere_principale }})</small>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('enseignant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="coefficient" class="form-label fw-medium">Coefficient <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="0.1" max="10"
                                   class="form-control @error('coefficient') is-invalid @enderror"
                                   id="coefficient" name="coefficient"
                                   value="{{ old('coefficient', 1) }}" required>
                            @error('coefficient') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary-600 w-100">
                            <i class="ri-add-line me-1"></i>Ajouter la matière
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Colonne droite : Liste des matières + séances ── --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ri-list-check me-2 text-primary-600"></i>Matières associées</h5>
                </div>
                <div class="card-body p-0">
                    @if($classeMatieres->isEmpty())
                        <div class="text-center py-5 text-secondary-light">
                            <i class="ri-book-line ri-2x mb-2 d-block"></i>
                            Aucune matière associée à cette classe.
                        </div>
                    @else
                        <div class="accordion accordion-flush" id="accordionMatieres">
                            @foreach($classeMatieres as $cm)
                            @php
                                $matiereSeances = $seances->get($cm->matiere_id, collect());
            					$matiereSeancesParJour = $matiereSeances->groupBy('jour_semaine');
                            @endphp
                            <div class="accordion-item border-bottom">
                                <div class="accordion-header">
                                    <button class="accordion-button collapsed px-4 py-3"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#matiere-{{ $cm->matiere_id }}"
                                            aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3 w-100 me-3">
                                            <span class="fw-semibold">{{ $cm->matiere->nom_matiere }}</span>
                                            <span class="badge bg-neutral-200 text-secondary-light ms-auto">
                                                Coef. {{ $cm->coefficient }}
                                            </span>
                                            <span class="badge bg-primary-100 text-primary-600">
                                                <i class="ri-user-line me-1"></i>{{ $cm->enseignant?->nom_complet ?? 'Non assigné' }}
                                            </span>
                                            <span class="badge {{ $matiereSeances->count() > 0 ? 'bg-success-100 text-success-600' : 'bg-warning-100 text-warning-600' }}">
                                                {{ $matiereSeances->count() }} séance(s)
                                            </span>
                                        </div>
                                    </button>
                                </div>

                                <div id="matiere-{{ $cm->matiere_id }}"
                                     class="accordion-collapse collapse"
                                     data-bs-parent="#accordionMatieres">
                                    <div class="accordion-body bg-light p-3">

                                        {{-- ── Modifier la matière (coef + enseignant) ── --}}
                                        <div class="card mb-3">
                                            <div class="card-header py-2 bg-white">
                                                <small class="fw-semibold text-secondary-light text-uppercase">Modifier</small>
                                            </div>
                                            <div class="card-body py-2">
                                                <form action="{{ route('admin.classe-matieres.update', [$classeAnnee, $cm->matiere]) }}"
                                                      method="POST"
                                                      class="d-flex align-items-end gap-3 flex-wrap">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="flex-grow-1" style="min-width:160px;">
                                                        <label class="form-label small mb-1">Enseignant</label>
                                                        <select name="enseignant_id" class="form-select form-select-sm">
                                                            @foreach($enseignants as $ens)
                                                                <option value="{{ $ens->id }}"
                                                                    {{ $cm->enseignant_id == $ens->id ? 'selected' : '' }}>
                                                                    {{ $ens->nom_complet }}
                                                                    @if ($ens->matiere_principale)
                                                                        <small>({{ $ens->matiere_principale }})</small>
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div style="width:90px;">
                                                        <label class="form-label small mb-1">Coefficient</label>
                                                        <input type="number" step="0.1" min="0.1" max="10"
                                                               name="coefficient"
                                                               value="{{ $cm->coefficient }}"
                                                               class="form-control form-control-sm">
                                                    </div>

                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="ri-save-line me-1"></i>Mettre à jour
                                                    </button>

                                                </form>
                                                <form action="{{ route('admin.classe-matieres.destroy', [$classeAnnee, $cm->matiere]) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Supprimer cette matière et toutes ses séances ?')"
                                                      class="mb-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- ── Séances existantes ── --}}
                                        <div class="card mb-3">
                                            <div class="card-header py-2 bg-white d-flex align-items-center justify-content-between">
                                                <small class="fw-semibold text-secondary-light text-uppercase">
                                                    <i class="ri-time-line me-1"></i>Séances planifiées
                                                </small>
                                            </div>
                                            <div class="card-body p-0">
                                                @if($matiereSeances->isEmpty())
                                                    <p class="text-center text-secondary-light py-3 mb-0 text-sm">
                                                        Aucune séance planifiée.
                                                    </p>
                                                @else
                                                    <div class="table-responsive">
                                                        <table class="table table-sm mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Jour</th>
                                                                    <th>Début</th>
                                                                    <th>Fin</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($matiereSeances->sortBy(['jour_semaine', 'heure_debut']) as $seance)
                                                                <tr>
                                                                    <td>
                                                                        {{-- Modifier la séance --}}
                                                                        <form action="{{ route('admin.classe-matieres.seances.update', [$classeAnnee, $cm->matiere, $seance]) }}"
                                                                              method="POST"
                                                                              class="d-flex align-items-center gap-2 flex-wrap">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <select name="jour_semaine" class="form-select form-select-sm" style="width:110px;">
                                                                                @foreach($jours as $idx => $nom)
                                                                                    <option value="{{ $idx }}" {{ $seance->jour_semaine == $idx ? 'selected' : '' }}>{{ $nom }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                    </td>
                                                                    <td>
                                                                            <input type="time" name="heure_debut"
                                                                                   value="{{ $seance->heure_debut }}"
                                                                                   class="form-control form-control-sm" style="width:100px;">
                                                                    </td>
                                                                    <td>
                                                                            <input type="time" name="heure_fin"
                                                                                   value="{{ $seance->heure_fin }}"
                                                                                   class="form-control form-control-sm" style="width:100px;">
                                                                    </td>
                                                                    <td>
                                                                            <div class="d-flex gap-1">
                                                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                                    <i class="ri-save-line"></i>
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                        <form action="{{ route('admin.classe-matieres.seances.destroy', [$classeAnnee, $cm->matiere, $seance]) }}"
                                                                              method="POST"
                                                                              onsubmit="return confirm('Supprimer cette séance ?')"
                                                                              class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                                <i class="ri-delete-bin-line"></i>
                                                                            </button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- ── Ajouter une séance ── --}}
                                        <div class="card">
                                            <div class="card-header py-2 bg-white">
                                                <small class="fw-semibold text-secondary-light text-uppercase">
                                                    <i class="ri-add-circle-line me-1"></i>Ajouter une séance
                                                </small>
                                            </div>
                                            <div class="card-body py-2">
                                                <form action="{{ route('admin.classe-matieres.seances.store', [$classeAnnee, $cm->matiere]) }}"
                                                      method="POST"
                                                      class="d-flex align-items-end gap-3 flex-wrap">
                                                    @csrf

                                                    <div style="min-width:120px;">
                                                        <label class="form-label small mb-1">Jour</label>
                                                        <select name="jour_semaine" class="form-select form-select-sm" required>
                                                            @foreach($jours as $idx => $nom)
                                                                <option value="{{ $idx }}">{{ $nom }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label class="form-label small mb-1">Heure début</label>
                                                        <input type="time" name="heure_debut"
                                                               class="form-control form-control-sm"
                                                               value="08:00" required>
                                                    </div>

                                                    <div>
                                                        <label class="form-label small mb-1">Heure fin</label>
                                                        <input type="time" name="heure_fin"
                                                               class="form-control form-control-sm"
                                                               value="09:00" required>
                                                    </div>

                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="ri-add-line me-1"></i>Ajouter
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                    </div>{{-- fin accordion-body --}}
                                </div>{{-- fin accordion-collapse --}}
                            </div>{{-- fin accordion-item --}}
                            @endforeach
                        </div>{{-- fin accordion --}}
                    @endif
                </div>
            </div>
        </div>

        @include('back.pages.classe_matieres._emploi_du_temps')
    </div>{{-- fin row --}}
</div>
@endsection
