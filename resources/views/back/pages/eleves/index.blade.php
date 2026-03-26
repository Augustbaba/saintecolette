@extends('back.layouts.master')

@section('content')
<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Liste des élèves</h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Élèves</span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.eleves.import.create') }}" class="btn btn-primary-600 d-flex align-items-center gap-6">
                <i class="ri-upload-line"></i> Importer
            </a>
        </div>
    </div>

    <div class="card mb-24">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.eleves.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="classe_annee_id" class="form-label">
                        Classe (année active : {{ $anneeActive->libelle ?? 'Non définie' }})
                    </label>
                    <select name="classe_annee_id" id="classe_annee_id" class="form-select">
                        <option value="">-- Toutes les classes --</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ $selectedClasseId == $classe->id ? 'selected' : '' }}>
                                {{ $classe->classe->niveau->nom }} {{ $classe->classe->suffixe }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Nom, prénom ou matricule..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ri-search-line me-1"></i> Filtrer
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.eleves.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ri-refresh-line me-1"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($selectedClasseId || request('search'))
        <div class="card">
            <div class="card-body p-0">
                @if($eleves->isEmpty())
                    <div class="text-center py-5">
                        <i class="ri-user-unfollow-line fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Aucun élève trouvé</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Sexe</th>
                                    <th>Date naissance</th>
                                    <th>Téléphone</th>
                                    <th>Parent</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($eleves as $eleve)
                                <tr>
                                    <td>
                                        <code class="small">{{ $eleve->matricule }}</code>
                                    </td>
                                    <td><strong>{{ $eleve->nom }}</strong></td>
                                    <td>{{ $eleve->prenom }}</td>
                                    <td>
                                        @if($eleve->sexe == 'M')
                                            <span class="badge bg-info">Masculin</span>
                                        @elseif($eleve->sexe == 'F')
                                            <span class="badge bg-danger">Féminin</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($eleve->date_naissance)
                                            {{ $eleve->date_naissance->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($eleve->telephone)
                                            <code class="small">{{ $eleve->telephone }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($eleve->parentPrincipal)
                                            <span class="badge bg-success">
                                                {{ $eleve->parentPrincipal->nom }} {{ $eleve->parentPrincipal->prenom }}
                                            </span>
                                            @if($eleve->parentPrincipal->telephone)
                                                <br><small class="text-muted">{{ $eleve->parentPrincipal->telephone }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Aucun parent</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($eleve->statut == 'actif')
                                            <span class="badge bg-success">Actif</span>
                                        @elseif($eleve->statut == 'inactif')
                                            <span class="badge bg-secondary">Inactif</span>
                                        @else
                                            <span class="badge bg-dark">Ancien</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.eleves.show', $eleve->id) }}">
                                                        <i class="ri-eye-line me-2"></i> Voir
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.eleves.edit', $eleve->id) }}">
                                                        <i class="ri-pencil-line me-2"></i> Modifier
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.eleves.destroy', $eleve->id) }}" method="POST" 
                                                          onsubmit="return confirm('Supprimer définitivement cet élève ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="ri-delete-bin-line me-2"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Affichage de {{ $eleves->firstItem() ?? 0 }} à {{ $eleves->lastItem() ?? 0 }} 
                                    sur {{ $eleves->total() }} élève(s)
                                </small>
                            </div>
                            <div>
                                {{ $eleves->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="ri-information-line me-2"></i>
            Veuillez sélectionner une classe ou effectuer une recherche pour afficher les élèves.
        </div>
    @endif
</div>
@endsection