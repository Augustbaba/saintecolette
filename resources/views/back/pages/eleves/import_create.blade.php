@extends('back.layouts.master')

@section('content')
<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Importer des élèves</h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="{{ route('admin.eleves.index') }}" class="text-secondary-light hover-text-primary hover-underline">Élèves</a>
                <span class="text-secondary-light">/ Importer</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-upload-2-line me-2 text-primary"></i>
                        Charger le fichier Excel
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-checkbox-circle-line me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri-error-warning-line me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.eleves.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="classe_annee_id" class="form-label fw-semibold">
                                Classe &amp; Année scolaire <span class="text-danger">*</span>
                            </label>
                            <select name="classe_annee_id" id="classe_annee_id"
                                    class="form-select @error('classe_annee_id') is-invalid @enderror" required>
                                <option value="">— Sélectionnez la classe et l'année —</option>
                                @foreach($classesAnnees as $ca)
                                    <option value="{{ $ca->id }}" {{ old('classe_annee_id') == $ca->id ? 'selected' : '' }}>
                                        {{ $ca->classe->niveau->nom }} {{ $ca->classe->suffixe }}
                                        — {{ $ca->anneeScolaire->libelle }}
                                    </option>
                                @endforeach
                            </select>
                            @error('classe_annee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sheet_name" class="form-label fw-semibold">
                                Nom de l'onglet Excel <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="sheet_name" id="sheet_name"
                                   class="form-control @error('sheet_name') is-invalid @enderror"
                                   placeholder="Ex : Feuil1, A, 1ère AB, Tle C …"
                                   value="{{ old('sheet_name', 'Feuil1') }}" required>
                            @error('sheet_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Saisir le nom <strong>exact</strong> de l'onglet (respecter majuscules/accents).</div>
                        </div>

                        <div class="mb-4">
                            <label for="file" class="form-label fw-semibold">
                                Fichier Excel <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="file" id="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   accept=".xlsx,.xls,.csv" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Formats acceptés : xlsx, xls, csv — Taille max : 5 Mo</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-eye-line me-1"></i> Prévisualiser
                            </button>
                            <a href="{{ route('admin.eleves.index') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="ri-information-line text-info me-1"></i>
                        Format du fichier attendu
                    </h6>
                    <p class="text-muted small mb-2">
                        Le fichier doit contenir les colonnes suivantes <strong>en ligne 1</strong> :
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered small mb-3">
                            <thead class="table-primary">
                                32
                                    <th>Colonne</th>
                                    <th>Obligatoire</th>
                                    <th>Remarque</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>Matricule</code></td>
                                    <td><span class="badge bg-secondary">Non</span></td>
                                    <td>Conservé s'il est présent</td>
                                </tr>
                                <tr>
                                    <td><code>Nom</code></td>
                                    <td><span class="badge bg-danger">Oui</span></td>
                                    <td>Nom de famille de l'élève</td>
                                </tr>
                                <tr>
                                    <td><code>Prénoms</code></td>
                                    <td><span class="badge bg-danger">Oui</span></td>
                                    <td>Prénom(s) de l'élève</td>
                                </tr>
                                <tr>
                                    <td><code>Sexe</code></td>
                                    <td><span class="badge bg-secondary">Non</span></td>
                                    <td>M ou F (Masculin/Féminin)</td>
                                </tr>
                                <tr>
                                    <td><code>Date Naissance</code></td>
                                    <td><span class="badge bg-secondary">Non</span></td>
                                    <td>Format: JJ/MM/AAAA ou AAAA-MM-JJ</td>
                                </tr>
                                <tr>
                                    <td><code>Classe</code></td>
                                    <td><span class="badge bg-secondary">Non</span></td>
                                    <td>Informatif uniquement</td>
                                </tr>
                                <tr>
                                    <td><code>N° GSM</code></td>
                                    <td><span class="badge bg-secondary">Non</span></td>
                                    <td>Sert à lier le parent s'il existe déjà</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info p-2 small mb-0">
                        <i class="ri-phone-line me-1"></i>
                        <strong>Téléphone :</strong> les numéros au format <code>229XXXXXXXX</code> sont convertis en <code>22901XXXXXXXX</code>.<br>
                        <strong class="text-warning">⚠ Important :</strong> Le parent n'est pas créé automatiquement. Seuls les parents existants sont liés à l'élève.
                    </div>
                    <div class="alert alert-secondary p-2 small mt-2 mb-0">
                        <i class="ri-id-card-line me-1"></i>
                        <strong>Matricule :</strong> 
                        <code>24-0138</code> → Format automatique avec "01"<br>
                        Si vide, génération auto : <code>25-010001</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection