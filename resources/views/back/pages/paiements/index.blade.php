{{-- ============================================================
     Historique de tous les paiements
     back/pages/paiements/index.blade.php
     ============================================================ --}}
@extends('back.layouts.master')

@section('content')
<div class="dashboard-main-body">

    {{-- ── Breadcrumb ── --}}
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Historique des paiements</h1>
            <div class="d-flex align-items-center gap-8 flex-wrap">
                <a href="{{ route('admin.dashboard') }}"
                   class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/</span>
                <span class="text-secondary-light">Paiements</span>
            </div>
        </div>
        <a href="{{ route('admin.paiements.create') }}"
           class="btn d-flex align-items-center gap-8"
           style="background:#8B1A1A;color:#fff;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:10px 20px;border-radius:3px;">
            <i class="ri-add-line"></i> Nouveau paiement
        </a>
    </div>

    {{-- ── Cartes KPI ── --}}
    <div class="row gy-16 mb-24">
        <div class="col-xxl-3 col-sm-6">
            <div class="card h-100 radius-8" style="border-left:4px solid #8B1A1A;">
                <div class="card-body p-20 d-flex align-items-center gap-16">
                    <span class="w-48-px h-48-px rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                          style="background:rgba(139,26,26,.1);">
                        <i class="ri-money-dollar-circle-line" style="font-size:22px;color:#8B1A1A;"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light mb-4" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;">Total encaissé</p>
                        <h5 class="fw-bold mb-0" style="color:#8B1A1A;">
                            {{ number_format($stats['total_encaisse'], 0, ',', ' ') }} <small style="font-size:12px;">FCFA</small>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card h-100 radius-8" style="border-left:4px solid #C8922A;">
                <div class="card-body p-20 d-flex align-items-center gap-16">
                    <span class="w-48-px h-48-px rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                          style="background:rgba(200,146,42,.12);">
                        <i class="ri-receipt-line" style="font-size:22px;color:#C8922A;"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light mb-4" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;">Nb paiements</p>
                        <h5 class="fw-bold mb-0" style="color:#C8922A;">{{ $stats['nb_paiements'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card h-100 radius-8" style="border-left:4px solid #2e7d32;">
                <div class="card-body p-20 d-flex align-items-center gap-16">
                    <span class="w-48-px h-48-px rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                          style="background:rgba(46,125,50,.1);">
                        <i class="ri-user-follow-line" style="font-size:22px;color:#2e7d32;"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light mb-4" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;">Élèves à jour</p>
                        <h5 class="fw-bold mb-0" style="color:#2e7d32;">{{ $stats['eleves_a_jour'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card h-100 radius-8" style="border-left:4px solid #e65100;">
                <div class="card-body p-20 d-flex align-items-center gap-16">
                    <span class="w-48-px h-48-px rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                          style="background:rgba(230,81,0,.1);">
                        <i class="ri-error-warning-line" style="font-size:22px;color:#e65100;"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light mb-4" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;">Débiteurs</p>
                        <h5 class="fw-bold mb-0" style="color:#e65100;">{{ $stats['eleves_debiteurs'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filtres ── --}}
    <div class="card mb-24">
        <div class="card-body p-16">
            <form method="GET" action="{{ route('admin.paiements.index') }}"
                  class="d-flex flex-wrap gap-12 align-items-end">

                <div style="min-width:200px;flex:1;">
                    <label class="form-label text-secondary-light mb-4"
                           style="font-size:10px;letter-spacing:1px;text-transform:uppercase;font-weight:700;">
                        Classe
                    </label>
                    <select name="classe_annee_id" class="form-select form-select-sm"
                            onchange="this.form.submit()">
                        <option value="">Toutes les classes</option>
                        @foreach($classesAnnees as $ca)
                            <option value="{{ $ca->id }}"
                                {{ request('classe_annee_id') == $ca->id ? 'selected' : '' }}>
                                {{ $ca->classe->niveau->nom }} {{ $ca->classe->suffixe }}
                                — {{ $ca->anneeScolaire->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width:160px;">
                    <label class="form-label text-secondary-light mb-4"
                           style="font-size:10px;letter-spacing:1px;text-transform:uppercase;font-weight:700;">
                        Mode de paiement
                    </label>
                    <select name="mode_paiement" class="form-select form-select-sm"
                            onchange="this.form.submit()">
                        <option value="">Tous les modes</option>
                        @foreach(['especes'=>'Espèces','mobile_money'=>'Mobile Money','virement'=>'Virement','carte'=>'Carte','cheque'=>'Chèque'] as $val=>$label)
                            <option value="{{ $val }}"
                                {{ request('mode_paiement') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width:140px;">
                    <label class="form-label text-secondary-light mb-4"
                           style="font-size:10px;letter-spacing:1px;text-transform:uppercase;font-weight:700;">
                        Du
                    </label>
                    <input type="date" name="date_debut" class="form-control form-control-sm"
                           value="{{ request('date_debut') }}">
                </div>

                <div style="min-width:140px;">
                    <label class="form-label text-secondary-light mb-4"
                           style="font-size:10px;letter-spacing:1px;text-transform:uppercase;font-weight:700;">
                        Au
                    </label>
                    <input type="date" name="date_fin" class="form-control form-control-sm"
                           value="{{ request('date_fin') }}">
                </div>

                <div style="min-width:200px;flex:1;">
                    <label class="form-label text-secondary-light mb-4"
                           style="font-size:10px;letter-spacing:1px;text-transform:uppercase;font-weight:700;">
                        Recherche élève
                    </label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Nom, prénom, matricule…"
                           value="{{ request('search') }}">
                </div>

                <div class="d-flex gap-8">
                    <button type="submit"
                            class="btn btn-sm d-flex align-items-center gap-6"
                            style="background:#8B1A1A;color:#fff;font-size:11px;font-weight:700;padding:7px 16px;border-radius:3px;">
                        <i class="ri-search-line"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.paiements.index') }}"
                       class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-6"
                       style="font-size:11px;padding:7px 14px;border-radius:3px;">
                        <i class="ri-refresh-line"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tableau principal ── --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between py-16 px-20"
             style="border-bottom:1px solid rgba(0,0,0,.06);">
            <h6 class="mb-0 fw-semibold text-primary-light d-flex align-items-center gap-8">
                <i class="ri-list-check" style="color:#C8922A;"></i>
                {{ $paiements->total() }} paiement(s) trouvé(s)
            </h6>
            <span class="text-secondary-light" style="font-size:12px;">
                Page {{ $paiements->currentPage() }} / {{ $paiements->lastPage() }}
            </span>
        </div>

        <div class="card-body p-0">
            @if($paiements->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-48"
                     style="opacity:.45;">
                    <i class="ri-inbox-2-line" style="font-size:3rem;color:#C8922A;"></i>
                    <p class="mt-12 text-secondary-light">Aucun paiement trouvé.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead style="background:#faf7f2;">
                            <tr>
                                @foreach([
                                    'Date','Élève','Classe','Tranche','Mode','Référence','Montant','Actions'
                                ] as $col)
                                    <th class="px-16 py-12 text-secondary-light fw-semibold"
                                        style="font-size:10px;letter-spacing:1px;text-transform:uppercase;
                                               border-bottom:2px solid rgba(139,26,26,.12);white-space:nowrap;">
                                        {{ $col }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paiements as $p)
                                <tr style="border-bottom:1px solid rgba(0,0,0,.04);transition:background .15s;"
                                    onmouseover="this.style.background='#faf7f2'"
                                    onmouseout="this.style.background=''">

                                    {{-- Date --}}
                                    <td class="px-16 py-12" style="white-space:nowrap;">
                                        <span class="fw-semibold" style="font-size:13px;">
                                            {{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}
                                        </span>
                                        <small class="d-block text-secondary-light" style="font-size:10px;">
                                            {{ \Carbon\Carbon::parse($p->date_paiement)->diffForHumans() }}
                                        </small>
                                    </td>

                                    {{-- Élève --}}
                                    <td class="py-12">
                                        @php
                                            $initiales = strtoupper(
                                                mb_substr($p->eleve->nom ?? '?', 0, 1) .
                                                mb_substr($p->eleve->prenom ?? '', 0, 1)
                                            );
                                        @endphp
                                        <div class="d-flex align-items-center gap-10">
                                            <span class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                                  style="background:linear-gradient(135deg,#8B1A1A,#C8922A);font-size:11px;">
                                                {{ $initiales }}
                                            </span>
                                            <div>
                                                <span class="fw-semibold d-block">
                                                    {{ $p->eleve->nom ?? '—' }} {{ $p->eleve->prenom ?? '' }}
                                                </span>
                                                <small class="text-secondary-light" style="font-size:10px;">
                                                    {{ $p->eleve->matricule ?? '' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Classe --}}
                                    <td class="py-12 text-secondary-light" style="white-space:nowrap;">
                                        {{ optional(optional(optional($p->eleve)->classeAnnee)->classe)->niveau->nom ?? '—' }}
                                        {{ optional(optional(optional($p->eleve)->classeAnnee)->classe)->suffixe ?? '' }}
                                    </td>

                                    {{-- Tranche --}}
                                    <td class="py-12">
                                        @if($p->tranche)
                                            <span class="badge"
                                                  style="background:rgba(200,146,42,.15);color:#85600a;font-size:10px;padding:3px 8px;border-radius:4px;">
                                                {{ $p->tranche->ordre }}. {{ $p->tranche->libelle }}
                                            </span>
                                        @else
                                            <span class="text-secondary-light" style="font-size:11px;">Libre</span>
                                        @endif
                                    </td>

                                    {{-- Mode --}}
                                    <td class="py-12">
                                        @php
                                            [$ml, $mc] = match($p->mode_paiement) {
                                                'especes'      => ['Espèces',       '#2e7d32'],
                                                'mobile_money' => ['Mobile Money',  '#1565c0'],
                                                'virement'     => ['Virement',      '#6a1b9a'],
                                                'carte'        => ['Carte',         '#00695c'],
                                                'cheque'       => ['Chèque',        '#4e342e'],
                                                default        => [$p->mode_paiement, '#37474f'],
                                            };
                                        @endphp
                                        <span class="badge"
                                              style="background:{{ $mc }}20;color:{{ $mc }};font-size:10px;padding:3px 8px;border-radius:4px;">
                                            {{ $ml }}
                                        </span>
                                    </td>

                                    {{-- Référence --}}
                                    <td class="py-12" style="font-family:monospace;font-size:11px;color:#6b6055;">
                                        {{ $p->reference ?? '—' }}
                                    </td>

                                    {{-- Montant --}}
                                    <td class="py-12 fw-bold" style="color:#8B1A1A;white-space:nowrap;">
                                        {{ number_format($p->montant, 0, ',', ' ') }} <small>FCFA</small>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="py-12 px-16">
                                        <div class="d-flex align-items-center gap-8">
                                            <a href="{{ route('admin.paiements.create', ['classe_annee_id' => optional(optional($p->eleve)->classeAnnee)->id, 'eleve_id' => $p->eleve_id]) }}"
                                               class="w-28-px h-28-px rounded d-flex align-items-center justify-content-center"
                                               style="background:rgba(200,146,42,.12);color:#C8922A;"
                                               title="Voir l'élève">
                                                <i class="ri-eye-line" style="font-size:14px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background:#faf7f2;">
                            <tr>
                                <td colspan="6" class="px-16 py-12 fw-bold text-primary-light">
                                    Total de la page
                                </td>
                                <td class="py-12 fw-bold" style="color:#C8922A;font-size:15px;">
                                    {{ number_format($paiements->sum('montant'), 0, ',', ' ') }} FCFA
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center px-20 py-16"
                     style="border-top:1px solid rgba(0,0,0,.06);">
                    <span class="text-secondary-light" style="font-size:12px;">
                        {{ $paiements->firstItem() }}–{{ $paiements->lastItem() }}
                        sur {{ $paiements->total() }} paiements
                    </span>
                    {{ $paiements->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection