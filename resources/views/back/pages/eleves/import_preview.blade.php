@extends('back.layouts.master')

@section('styles')
<style>
    .table-preview {
        font-size: 0.875rem;
    }
    .table-preview th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .error-cell {
        background-color: #fff3cd;
        color: #856404;
    }
    .warning-badge {
        background-color: #ffc107;
        color: #000;
    }
    .parent-found {
        background-color: #d4edda;
        color: #155724;
    }
    .parent-not-found {
        background-color: #f8d7da;
        color: #721c24;
    }
    .select-all-row {
        background-color: #e7f3ff;
    }
    .import-summary {
        position: sticky;
        top: 20px;
    }
</style>
@endsection

@section('content')
<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Prévisualisation des données</h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Élèves / Import / Prévisualisation</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-database-2-line me-2 text-primary"></i>
                        Données extraites du fichier
                    </h5>
                    <small class="text-muted">Classe sélectionnée : <strong>{{ $classeAnnee->classe->niveau->nom }} {{ $classeAnnee->classe->suffixe }}</strong> ({{ $classeAnnee->anneeScolaire->libelle }})</small>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('admin.eleves.import.store') }}" method="POST" id="importForm">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table table-preview table-hover mb-0">
                                <thead>
                                    <tr class="select-all-row">
                                        <th width="40">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="selectAll">
                                                <label class="form-check-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                        <th>#</th>
                                        <th>Matricule</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Sexe</th>
                                        <th>Date Naiss.</th>
                                        <th>Téléphone</th>
                                        <th>Parent</th>
                                        <th>Classe (Excel)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $item)
                                        @php
                                            $hasError = isset($errors[$item['line_number']]);
                                            $errorMessages = $hasError ? (is_array($errors[$item['line_number']]) 
                                                ? implode(', ', $errors[$item['line_number']]) 
                                                : $errors[$item['line_number']]) : '';
                                            
                                            // Vérifier si le message d'erreur contient "Matricule généré"
                                            $hasMatriculeGenerated = $hasError && (
                                                (is_array($errors[$item['line_number']]) 
                                                    && collect($errors[$item['line_number']])->contains(fn($e) => str_contains($e, 'Matricule généré')))
                                                || (is_string($errors[$item['line_number']]) && str_contains($errors[$item['line_number']], 'Matricule généré'))
                                            );
                                        @endphp
                                        <tr class="{{ $hasError ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           name="selected[]" 
                                                           value="{{ $item['index'] }}" 
                                                           class="form-check-input select-item"
                                                           {{ !$hasError ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>{{ $item['line_number'] }}</td>
                                            <td>
                                                <code>{{ $item['matricule'] }}</code>
                                                @if($hasMatriculeGenerated)
                                                    <i class="ri-information-line text-warning" title="{{ $errorMessages }}"></i>
                                                @endif
                                            </td>
                                            <td><strong>{{ $item['nom'] }}</strong></td>
                                            <td>{{ $item['prenom'] }}</td>
                                            <td>
                                                @if($item['sexe'])
                                                    <span class="badge bg-{{ $item['sexe'] == 'M' ? 'info' : 'danger' }}">
                                                        {{ $item['sexe'] }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['date_naissance'])
                                                    {{ \Carbon\Carbon::parse($item['date_naissance'])->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['tel_eleve'])
                                                    <code>{{ $item['tel_eleve'] }}</code>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="{{ str_contains($item['parent_status'], 'existant') ? 'parent-found' : (str_contains($item['parent_status'], 'Non lié') ? 'parent-not-found' : '') }}">
                                                <small>{{ $item['parent_status'] }}</small>
                                            </td>
                                            <td>
                                                @if($item['classe_excel'])
                                                    <span class="text-muted">{{ $item['classe_excel'] }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($hasError && $errorMessages)
                                            <tr class="error-cell">
                                                <td colspan="10" class="py-2 px-3">
                                                    <i class="ri-error-warning-line text-warning me-1"></i>
                                                    <small class="text-warning">{{ $errorMessages }}</small>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <i class="ri-inbox-line fs-1 text-muted"></i>
                                                <p class="text-muted mt-2">Aucune donnée valide trouvée dans le fichier</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if(count($data) > 0)
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2" id="selectedCount">{{ count($data) }}</span>
                                        <span class="text-muted">élève(s) sélectionné(s)</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.eleves.import.create') }}" class="btn btn-outline-secondary">
                                            <i class="ri-arrow-left-line me-1"></i> Retour
                                        </a>
                                        <button type="submit" class="btn btn-success" id="importBtn">
                                            <i class="ri-check-line me-1"></i> Importer les sélectionnés
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="import-summary">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="ri-bar-chart-2-line me-1"></i> Résumé</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Total lignes valides</small>
                            <h5 class="mb-0">{{ count($data) }}</h5>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Avec avertissements</small>
                            <h5 class="mb-0 text-warning">{{ count($errors) }}</h5>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted">Parents existants trouvés</small>
                            <h5 class="mb-0 text-success">
                                {{ collect($data)->filter(fn($d) => str_contains($d['parent_status'], 'existant'))->count() }}
                            </h5>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Sans parent (non créé)</small>
                            <h5 class="mb-0 text-secondary">
                                {{ collect($data)->filter(fn($d) => str_contains($d['parent_status'], 'Non lié'))->count() }}
                            </h5>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted">Avec matricule auto-généré</small>
                            <h5 class="mb-0 text-info">
                                {{ collect($data)->filter(function($d) use ($errors) {
                                    $lineErrors = $errors[$d['line_number']] ?? null;
                                    if (!$lineErrors) return false;
                                    
                                    if (is_array($lineErrors)) {
                                        return collect($lineErrors)->contains(fn($e) => str_contains($e, 'Matricule généré'));
                                    }
                                    
                                    return str_contains($lineErrors, 'Matricule généré');
                                })->count() }}
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="ri-information-line me-1"></i> Informations</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info small mb-2 p-2">
                            <i class="ri-check-line me-1"></i> <strong>Parents existants</strong><br>
                            Les élèves avec un parent existant seront automatiquement liés.
                        </div>
                        <div class="alert alert-secondary small mb-2 p-2">
                            <i class="ri-user-unfollow-line me-1"></i> <strong>Sans parent</strong><br>
                            Aucun parent ne sera créé automatiquement.
                        </div>
                        <div class="alert alert-warning small mb-0 p-2">
                            <i class="ri-alert-line me-1"></i> <strong>Lignes avec avertissement</strong><br>
                            Les lignes avec des avertissements peuvent être importées, mais vérifiez les informations.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sélectionner/déselectionner tout
        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.select-item');
        const selectedCountSpan = document.getElementById('selectedCount');
        const importBtn = document.getElementById('importBtn');

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.select-item:checked');
            const count = checked.length;
            if (selectedCountSpan) {
                selectedCountSpan.textContent = count;
            }
            
            // Désactiver le bouton d'import si aucune ligne sélectionnée
            if (importBtn) {
                importBtn.disabled = count === 0;
                if (count === 0) {
                    importBtn.classList.add('opacity-50');
                } else {
                    importBtn.classList.remove('opacity-50');
                }
            }
            
            // Mettre à jour le "select all"
            if (selectAllCheckbox) {
                const totalItems = itemCheckboxes.length;
                const checkedItems = document.querySelectorAll('.select-item:checked').length;
                selectAllCheckbox.checked = totalItems > 0 && checkedItems === totalItems;
                selectAllCheckbox.indeterminate = checkedItems > 0 && checkedItems < totalItems;
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateSelectedCount();
            });
        }

        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // Initialiser le compteur
        updateSelectedCount();

        // Confirmation avant import
        const importForm = document.getElementById('importForm');
        if (importForm) {
            importForm.addEventListener('submit', function(e) {
                const selectedCount = document.querySelectorAll('.select-item:checked').length;
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Veuillez sélectionner au moins un élève à importer.');
                } else {
                    if (!confirm(`Vous allez importer ${selectedCount} élève(s). Êtes-vous sûr ?`)) {
                        e.preventDefault();
                    }
                }
            });
        }
    });
</script>
@endsection