{{-- ============================================================
     Élèves débiteurs — ceux qui doivent encore de l'argent
     back/pages/paiements/debiteurs.blade.php
     ============================================================ --}}
@extends('back.layouts.master')

@section('content')
<div class="dashboard-main-body">

    {{-- ── Breadcrumb ── --}}
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Élèves débiteurs</h1>
            <div class="d-flex align-items-center gap-8 flex-wrap">
                <a href="{{ route('admin.dashboard') }}"
                   class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/</span>
                <a href="{{ route('admin.paiements.index') }}"
                   class="text-secondary-light hover-text-primary hover-underline">Paiements</a>
                <span class="text-secondary-light">/ Débiteurs</span>
            </div>
        </div>
        <div class="d-flex gap-12">
            <a href="{{ route('admin.paiements.index') }}"
               class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-8"
               style="border-radius:3px;font-size:12px;">
                <i class="ri-history-line"></i> Historique
            </a>
            <a href="{{ route('admin.paiements.create') }}"
               class="btn btn-sm d-flex align-items-center gap-8"
               style="background:#8B1A1A;color:#fff;font-size:12px;font-weight:700;padding:7px 16px;border-radius:3px;">
                <i class="ri-add-line"></i> Nouveau paiement
            </a>
        </div>
    </div>

    {{-- ── KPI résumé dettes ── --}}
    <div class="row gy-16 mb-24">
        <div class="col-xxl-3 col-sm-6">
            <div class="card h-100 radius-8" style="border-left:4px solid #e65100;">
                <div class="card-body p-20 d-flex align-items-center gap-16">
                    <span class="w-48-px h-48-px rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                          style="background:rgba(230,81,0,.1);">
                        <i class="ri-alarm-warning-line" style="font-size:22px;color:#e65100;"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light mb-4" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;">Total dû</p>
                        <h5 class="fw-bold mb-0" style="color:#e65100;">
                            {{ number_format($totalDu, 0, ',', ' ') }}
                            <small style="font-size:12px;">FCFA</small>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card h-100 radius-8" style="border-left:4px solid #8B1A1A;">
                <div class="card-body p-20 d-flex align-items-center gap-16">
                    <span class="w-48-px h-48-px rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                          style="background:rgba(139,26,26,.1);">
                        <i class="ri-group-line" style="font-size:22px;color:#8B1A1A;"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light mb-4" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;">Nb débiteurs</p>
                        <h5 class="fw-bold mb-0" style="color:#8B1A1A;">
                            {{ $debiteurs->total() }}
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
                        <i class="ri-percent-line" style="font-size:22px;color:#C8922A;"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light mb-4" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;">Dette moyenne</p>
                        <h5 class="fw-bold mb-0" style="color:#C8922A;">
                            {{ $debiteurs->total() > 0 ? number_format($totalDu / $debiteurs->total(), 0, ',', ' ') : 0 }}
                            <small style="font-size:12px;">FCFA</small>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card h-100 radius-8" style="border-left:4px solid #1565c0;">
                <div class="card-body p-20 d-flex align-items-center gap-16">
                    <span class="w-48-px h-48-px rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                          style="background:rgba(21,101,192,.1);">
                        <i class="ri-bank-line" style="font-size:22px;color:#1565c0;"></i>
                    </span>
                    <div>
                        <p class="text-secondary-light mb-4" style="font-size:11px;letter-spacing:1px;text-transform:uppercase;">Total scolarités</p>
                        <h5 class="fw-bold mb-0" style="color:#1565c0;">
                            {{ number_format($totalScolarites, 0, ',', ' ') }}
                            <small style="font-size:12px;">FCFA</small>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filtres ── --}}
    <div class="card mb-24">
        <div class="card-body p-16">
            <form method="GET" action="{{ route('admin.paiements.debiteurs') }}"
                  class="d-flex flex-wrap gap-12 align-items-end">

                <div style="min-width:220px;flex:1;">
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
                        Niveau de dette
                    </label>
                    <select name="niveau_dette" class="form-select form-select-sm"
                            onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <option value="critique" {{ request('niveau_dette') === 'critique' ? 'selected' : '' }}>
                            Critique (&gt; 75% dû)
                        </option>
                        <option value="eleve" {{ request('niveau_dette') === 'eleve' ? 'selected' : '' }}>
                            Élevé (50–75% dû)
                        </option>
                        <option value="partiel" {{ request('niveau_dette') === 'partiel' ? 'selected' : '' }}>
                            Partiel (&lt; 50% dû)
                        </option>
                    </select>
                </div>

                <div style="min-width:200px;flex:1;">
                    <label class="form-label text-secondary-light mb-4"
                           style="font-size:10px;letter-spacing:1px;text-transform:uppercase;font-weight:700;">
                        Recherche élève / parent
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
                    <a href="{{ route('admin.paiements.debiteurs') }}"
                       class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-6"
                       style="font-size:11px;padding:7px 14px;border-radius:3px;">
                        <i class="ri-refresh-line"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Liste débiteurs ── --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between py-16 px-20"
             style="border-bottom:1px solid rgba(0,0,0,.06);">
            <h6 class="mb-0 fw-semibold text-primary-light d-flex align-items-center gap-8">
                <i class="ri-error-warning-line" style="color:#e65100;"></i>
                {{ $debiteurs->total() }} élève(s) avec solde impayé
            </h6>
        </div>

        <div class="card-body p-0">
            @if($debiteurs->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-48"
                     style="opacity:.5;">
                    <i class="ri-checkbox-circle-line" style="font-size:3.5rem;color:#2e7d32;"></i>
                    <p class="mt-12 fw-semibold" style="color:#2e7d32;">
                        Excellent ! Tous les élèves sont à jour.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead style="background:#faf7f2;">
                            <tr>
                                @foreach(['Élève','Classe','Parent / Contact','Scolarité','Payé','Reste dû','Avancement','Actions'] as $col)
                                    <th class="px-16 py-12 text-secondary-light fw-semibold"
                                        style="font-size:10px;letter-spacing:1px;text-transform:uppercase;
                                               border-bottom:2px solid rgba(139,26,26,.12);white-space:nowrap;">
                                        {{ $col }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($debiteurs as $row)
                                @php
                                    $pct     = $row->montant_annuel > 0
                                             ? round(($row->total_paye / $row->montant_annuel) * 100)
                                             : 0;
                                    $reste   = $row->montant_annuel - $row->total_paye;

                                    // Couleur selon niveau de dette
                                    [$barColor, $badgeLabel, $badgeBg] = match(true) {
                                        $pct === 0          => ['#b71c1c', 'Aucun paiement', '#ffebee'],
                                        $pct < 25           => ['#c62828', 'Critique',        '#ffebee'],
                                        $pct < 50           => ['#e65100', 'Élevé',           '#fff3e0'],
                                        $pct < 75           => ['#f9a825', 'Partiel',         '#fffde7'],
                                        default             => ['#2e7d32', 'Presque soldé',   '#e8f5e9'],
                                    };

                                    $initiales = strtoupper(
                                        mb_substr($row->nom ?? '?', 0, 1) .
                                        mb_substr($row->prenom ?? '', 0, 1)
                                    );
                                @endphp
                                <tr style="border-bottom:1px solid rgba(0,0,0,.04);transition:background .15s;"
                                    onmouseover="this.style.background='#fdf5f5'"
                                    onmouseout="this.style.background=''">

                                    {{-- Élève --}}
                                    <td class="px-16 py-12">
                                        <div class="d-flex align-items-center gap-10">
                                            <span class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                                  style="background:linear-gradient(135deg,#8B1A1A,#C8922A);font-size:12px;">
                                                {{ $initiales }}
                                            </span>
                                            <div>
                                                <span class="fw-semibold d-block">
                                                    {{ $row->nom }} {{ $row->prenom }}
                                                </span>
                                                <small class="text-secondary-light" style="font-size:10px;">
                                                    {{ $row->matricule }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Classe --}}
                                    <td class="py-12 text-secondary-light" style="white-space:nowrap;">
                                        {{ $row->niveau_nom ?? '—' }} {{ $row->classe_suffixe ?? '' }}
                                    </td>

                                    {{-- Parent / contact --}}
                                    <td class="py-12">
                                        <span class="d-block fw-semibold" style="font-size:12px;">
                                            {{ $row->parent_nom ?? '—' }} {{ $row->parent_prenom ?? '' }}
                                        </span>
                                        @if($row->parent_telephone)
                                            <a href="tel:{{ $row->parent_telephone }}"
                                               class="d-flex align-items-center gap-4"
                                               style="font-size:11px;color:#1565c0;text-decoration:none;">
                                                <i class="ri-phone-line" style="font-size:12px;"></i>
                                                {{ $row->parent_telephone }}
                                            </a>
                                        @endif
                                        @if($row->parent_whatsapp)
                                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $row->parent_whatsapp) }}"
                                               target="_blank"
                                               class="d-flex align-items-center gap-4 mt-2"
                                               style="font-size:11px;color:#2e7d32;text-decoration:none;">
                                                <i class="ri-whatsapp-line" style="font-size:12px;"></i>
                                                WhatsApp
                                            </a>
                                        @endif
                                    </td>

                                    {{-- Scolarité totale --}}
                                    <td class="py-12" style="white-space:nowrap;">
                                        <span class="fw-semibold">
                                            {{ number_format($row->montant_annuel, 0, ',', ' ') }}
                                        </span>
                                        <small class="text-secondary-light"> FCFA</small>
                                    </td>

                                    {{-- Déjà payé --}}
                                    <td class="py-12" style="white-space:nowrap;color:#2e7d32;font-weight:600;">
                                        {{ number_format($row->total_paye, 0, ',', ' ') }}
                                        <small class="text-secondary-light fw-normal"> FCFA</small>
                                    </td>

                                    {{-- Reste dû --}}
                                    <td class="py-12" style="white-space:nowrap;">
                                        <span class="fw-bold" style="color:{{ $barColor }};font-size:14px;">
                                            {{ number_format($reste, 0, ',', ' ') }}
                                        </span>
                                        <small class="text-secondary-light fw-normal"> FCFA</small>
                                        <span class="d-block mt-2 badge"
                                              style="background:{{ $badgeBg }};color:{{ $barColor }};font-size:10px;padding:2px 7px;border-radius:4px;width:fit-content;">
                                            {{ $badgeLabel }}
                                        </span>
                                    </td>

                                    {{-- Barre d'avancement --}}
                                    <td class="py-12" style="min-width:120px;">
                                        <div class="d-flex align-items-center gap-8">
                                            <div class="flex-grow-1">
                                                <div style="background:#f0ece4;height:6px;border-radius:99px;overflow:hidden;">
                                                    <div style="width:{{ $pct }}%;height:100%;background:{{ $barColor }};border-radius:99px;transition:width .6s;"></div>
                                                </div>
                                            </div>
                                            <span style="font-size:11px;font-weight:700;color:{{ $barColor }};min-width:30px;">
                                                {{ $pct }}%
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="py-12 px-16">
                                        <div class="d-flex align-items-center gap-8">
                                            {{-- Enregistrer un paiement --}}
                                            <a href="{{ route('admin.paiements.create', ['classe_annee_id' => $row->classe_annee_id, 'eleve_id' => $row->eleve_id]) }}"
                                               class="w-28-px h-28-px rounded d-flex align-items-center justify-content-center"
                                               style="background:rgba(139,26,26,.1);color:#8B1A1A;"
                                               title="Enregistrer un paiement">
                                                <i class="ri-add-circle-line" style="font-size:14px;"></i>
                                            </a>

                                            {{-- WhatsApp relance --}}
                                            @if($row->parent_whatsapp)
                                                @php
                                                    $msg = urlencode(
                                                        "Bonjour " . ($row->parent_prenom ?? '') . " " . ($row->parent_nom ?? '') .
                                                        ", nous vous informons qu'un solde de " .
                                                        number_format($reste, 0, ',', ' ') .
                                                        " FCFA reste à régler pour la scolarité de " .
                                                        $row->prenom . " " . $row->nom .
                                                        ". Merci de régulariser votre situation. — CSBS Sainte Colette"
                                                    );
                                                @endphp
                                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $row->parent_whatsapp) }}?text={{ $msg }}"
                                                   target="_blank"
                                                   class="w-28-px h-28-px rounded d-flex align-items-center justify-content-center"
                                                   style="background:rgba(46,125,50,.1);color:#2e7d32;"
                                                   title="Relancer par WhatsApp">
                                                    <i class="ri-whatsapp-line" style="font-size:14px;"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background:#fff5f5;">
                            <tr>
                                <td colspan="4" class="px-16 py-12 fw-bold text-primary-light">
                                    Total des dettes (page courante)
                                </td>
                                <td class="py-12 fw-bold" style="color:#2e7d32;">
                                    {{ number_format($debiteurs->sum('total_paye'), 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-12 fw-bold" style="color:#e65100;font-size:15px;">
                                    {{ number_format($debiteurs->sum(fn($r) => $r->montant_annuel - $r->total_paye), 0, ',', ' ') }} FCFA
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center px-20 py-16"
                     style="border-top:1px solid rgba(0,0,0,.06);">
                    <span class="text-secondary-light" style="font-size:12px;">
                        {{ $debiteurs->firstItem() }}–{{ $debiteurs->lastItem() }}
                        sur {{ $debiteurs->total() }} débiteur(s)
                    </span>
                    {{ $debiteurs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection