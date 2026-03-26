{{-- ============================================================
     Enregistrement d'un paiement de scolarité
     back/pages/paiements/create.blade.php
     ============================================================ --}}
@extends('back.layouts.master')

@section('content')
<div class="dashboard-main-body">

    {{-- ── Breadcrumb ── --}}
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Enregistrer un paiement</h1>
            <div class="d-flex align-items-center gap-8 flex-wrap">
                <a href="{{ route('admin.dashboard') }}"
                   class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/</span>
                <a href="{{ route('admin.paiements.index') }}"
                   class="text-secondary-light hover-text-primary hover-underline">Paiements</a>
                <span class="text-secondary-light">/ Nouveau</span>
            </div>
        </div>
        <a href="{{ route('admin.paiements.index') }}"
           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-8">
            <i class="ri-arrow-left-line"></i> Historique
        </a>
    </div>

    {{-- ── Message succès ── --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-12 mb-24 radius-8 border-0"
             style="background:#e8f5e9; color:#1b5e20; border-left:4px solid #4caf50 !important;">
            <i class="ri-checkbox-circle-line text-xl"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="row gy-24">

        {{-- ══════════════════════════════════════════
             COLONNE GAUCHE — Sélection classe + élève
             ══════════════════════════════════════════ --}}
        <div class="col-xl-4 col-lg-5">

            {{-- Card : Choisir la classe --}}
            <div class="card mb-24">
                <div class="card-header d-flex align-items-center gap-10 py-16 px-20"
                     style="border-bottom:1px solid rgba(0,0,0,.06);">
                    <span class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center"
                          style="background:rgba(139,26,26,.1);">
                        <i class="ri-building-line text-sm" style="color:#8B1A1A;"></i>
                    </span>
                    <h6 class="mb-0 fw-semibold text-primary-light">1. Choisir la classe</h6>
                </div>
                <div class="card-body p-20">
                    <form method="GET" action="{{ route('admin.paiements.create') }}" id="form-classe">
                        <div class="mb-16">
                            <label class="form-label fw-semibold text-secondary-light"
                                   style="font-size:11px; letter-spacing:1px; text-transform:uppercase;">
                                Classe – Année scolaire
                            </label>
                            <select name="classe_annee_id" id="sel-classe"
                                    class="form-select"
                                    onchange="this.form.submit()">
                                <option value="">— Sélectionner —</option>
                                @foreach($classesAnnees as $ca)
                                    <option value="{{ $ca->id }}"
                                        {{ $classeAnneeId == $ca->id ? 'selected' : '' }}>
                                        {{ $ca->classe->niveau->nom }}
                                        {{ $ca->classe->suffixe }}
                                        — {{ $ca->anneeScolaire->libelle }}
                                        @if($ca->scolarite)
                                            ({{ number_format($ca->scolarite->montant_annuel, 0, ',', ' ') }} FCFA)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    {{-- Résumé scolarité si classe sélectionnée --}}
                    @if($scolarite)
                        <div class="radius-8 p-16 mt-4"
                             style="background:rgba(139,26,26,.05); border:1px solid rgba(139,26,26,.15);">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <span class="text-secondary-light" style="font-size:11px;">Montant annuel</span>
                                <span class="fw-bold" style="color:#8B1A1A; font-size:15px;">
                                    {{ number_format($scolarite->montant_annuel, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                            @if($scolarite->description)
                                <p class="text-secondary-light mb-0" style="font-size:11px;">
                                    {{ $scolarite->description }}
                                </p>
                            @endif

                            {{-- Tranches --}}
                            @if($tranches->count())
                                <div class="mt-12 pt-12" style="border-top:1px solid rgba(139,26,26,.12);">
                                    <p class="fw-semibold mb-8 text-secondary-light"
                                       style="font-size:10px; letter-spacing:1px; text-transform:uppercase;">
                                        Tranches définies
                                    </p>
                                    @foreach($tranches as $t)
                                        <div class="d-flex justify-content-between align-items-center mb-6">
                                            <span class="text-secondary-light" style="font-size:12px;">
                                                {{ $t->ordre }}. {{ $t->libelle }}
                                                <small class="d-block" style="font-size:10px;">
                                                    Échéance : {{ \Carbon\Carbon::parse($t->date_echeance)->format('d/m/Y') }}
                                                </small>
                                            </span>
                                            <span class="fw-semibold" style="font-size:12px; color:#C8922A;">
                                                {{ number_format($t->montant, 0, ',', ' ') }} F
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card : Choisir l'élève --}}
            @if($classeAnneeId && $eleves->count())
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-10 py-16 px-20"
                         style="border-bottom:1px solid rgba(0,0,0,.06);">
                        <span class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center"
                              style="background:rgba(200,146,42,.12);">
                            <i class="ri-user-line text-sm" style="color:#C8922A;"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold text-primary-light">2. Choisir l'élève</h6>
                    </div>
                    <div class="card-body p-20">
                        <form method="GET" action="{{ route('admin.paiements.create') }}" id="form-eleve">
                            <input type="hidden" name="classe_annee_id" value="{{ $classeAnneeId }}">
                            <div class="mb-0">
                                <label class="form-label fw-semibold text-secondary-light"
                                       style="font-size:11px; letter-spacing:1px; text-transform:uppercase;">
                                    Élève ({{ $eleves->count() }} actifs)
                                </label>
                                <select name="eleve_id" class="form-select"
                                        onchange="this.form.submit()">
                                    <option value="">— Sélectionner un élève —</option>
                                    @foreach($eleves as $e)
                                        <option value="{{ $e->id }}"
                                            {{ $eleveId == $e->id ? 'selected' : '' }}>
                                            {{ $e->nom }} {{ $e->prenom }}
                                            ({{ $e->matricule }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($classeAnneeId && $eleves->isEmpty())
                <div class="alert d-flex align-items-center gap-12 radius-8"
                     style="background:#fff3e0; color:#e65100; border-left:4px solid #ff9800;">
                    <i class="ri-information-line"></i>
                    Aucun élève actif dans cette classe.
                </div>
            @endif

        </div>

        {{-- ══════════════════════════════════════════
             COLONNE DROITE — Fiche élève + formulaire
             ══════════════════════════════════════════ --}}
        <div class="col-xl-8 col-lg-7">

            @if($eleve && $scolarite)

                {{-- Fiche élève + solde --}}
                <div class="card mb-24">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center gap-16 flex-wrap">

                            {{-- Avatar / initiales --}}
                            @php
                                $initiales = strtoupper(mb_substr($eleve->nom, 0, 1) . mb_substr($eleve->prenom, 0, 1));
                            @endphp
                            <div class="w-56-px h-56-px rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                 style="background:linear-gradient(135deg,#8B1A1A,#C8922A); font-size:18px;">
                                {{ $initiales }}
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="mb-2 fw-bold text-primary-light">
                                    {{ $eleve->nom }} {{ $eleve->prenom }}
                                </h5>
                                <p class="mb-0 text-secondary-light" style="font-size:13px;">
                                    Matricule : <strong>{{ $eleve->matricule }}</strong>
                                    &nbsp;·&nbsp;
                                    {{ $eleve->classeAnnee->classe->niveau->nom ?? '' }}
                                    {{ $eleve->classeAnnee->classe->suffixe ?? '' }}
                                </p>
                            </div>

                            {{-- Indicateurs financiers --}}
                            @php
                                $reste = $scolarite->montant_annuel - $deja_paye;
                                $pct   = $scolarite->montant_annuel > 0
                                       ? round(($deja_paye / $scolarite->montant_annuel) * 100)
                                       : 0;
                            @endphp
                            <div class="d-flex gap-16 flex-wrap">
                                <div class="text-center">
                                    <div class="fw-bold" style="font-size:18px; color:#C8922A;">
                                        {{ number_format($deja_paye, 0, ',', ' ') }}
                                    </div>
                                    <div class="text-secondary-light" style="font-size:10px; letter-spacing:1px; text-transform:uppercase;">
                                        Déjà payé (FCFA)
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="fw-bold" style="font-size:18px; color:{{ $reste > 0 ? '#8B1A1A' : '#2e7d32' }};">
                                        {{ number_format(abs($reste), 0, ',', ' ') }}
                                    </div>
                                    <div class="text-secondary-light" style="font-size:10px; letter-spacing:1px; text-transform:uppercase;">
                                        {{ $reste > 0 ? 'Reste à payer (FCFA)' : 'Soldé ✓' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Barre de progression --}}
                        <div class="mt-16">
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-secondary-light" style="font-size:11px;">
                                    Progression du paiement
                                </span>
                                <span class="fw-semibold" style="font-size:11px; color:#8B1A1A;">
                                    {{ $pct }}%
                                </span>
                            </div>
                            <div class="progress" style="height:6px; border-radius:99px; background:#f0ece4;">
                                <div class="progress-bar"
                                     role="progressbar"
                                     style="width:{{ $pct }}%; background:{{ $pct >= 100 ? '#2e7d32' : '#8B1A1A' }}; border-radius:99px;"
                                     aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Historique des paiements de cet élève --}}
                @php
                    $historique = \App\Models\Paiement::with('tranche')
                        ->where('eleve_id', $eleve->id)
                        ->orderByDesc('date_paiement')
                        ->get();
                @endphp
                @if($historique->count())
                    <div class="card mb-24">
                        <div class="card-header py-14 px-20"
                             style="border-bottom:1px solid rgba(0,0,0,.06);">
                            <h6 class="mb-0 fw-semibold text-primary-light d-flex align-items-center gap-8">
                                <i class="ri-history-line" style="color:#C8922A;"></i>
                                Historique des paiements
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0"
                                       style="font-size:13px;">
                                    <thead style="background:#faf7f2;">
                                        <tr>
                                            <th class="px-20 py-12 text-secondary-light fw-semibold"
                                                style="font-size:10px; letter-spacing:1px; text-transform:uppercase; border-bottom:1px solid rgba(0,0,0,.06);">
                                                Date
                                            </th>
                                            <th class="py-12 text-secondary-light fw-semibold"
                                                style="font-size:10px; letter-spacing:1px; text-transform:uppercase; border-bottom:1px solid rgba(0,0,0,.06);">
                                                Tranche
                                            </th>
                                            <th class="py-12 text-secondary-light fw-semibold"
                                                style="font-size:10px; letter-spacing:1px; text-transform:uppercase; border-bottom:1px solid rgba(0,0,0,.06);">
                                                Mode
                                            </th>
                                            <th class="py-12 text-secondary-light fw-semibold"
                                                style="font-size:10px; letter-spacing:1px; text-transform:uppercase; border-bottom:1px solid rgba(0,0,0,.06);">
                                                Référence
                                            </th>
                                            <th class="py-12 pe-20 text-end text-secondary-light fw-semibold"
                                                style="font-size:10px; letter-spacing:1px; text-transform:uppercase; border-bottom:1px solid rgba(0,0,0,.06);">
                                                Montant
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($historique as $p)
                                            <tr style="border-bottom:1px solid rgba(0,0,0,.04);">
                                                <td class="px-20 py-12">
                                                    {{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}
                                                </td>
                                                <td class="py-12 text-secondary-light">
                                                    {{ $p->tranche ? $p->tranche->libelle : '—' }}
                                                </td>
                                                <td class="py-12">
                                                    @php
                                                        $modeLabel = match($p->mode_paiement) {
                                                            'especes'      => 'Espèces',
                                                            'mobile_money' => 'Mobile Money',
                                                            'virement'     => 'Virement',
                                                            'carte'        => 'Carte',
                                                            'cheque'       => 'Chèque',
                                                            default        => $p->mode_paiement,
                                                        };
                                                        $modeColor = match($p->mode_paiement) {
                                                            'especes'      => '#2e7d32',
                                                            'mobile_money' => '#1565c0',
                                                            'virement'     => '#6a1b9a',
                                                            default        => '#37474f',
                                                        };
                                                    @endphp
                                                    <span class="badge"
                                                          style="background:{{ $modeColor }}20; color:{{ $modeColor }}; font-size:10px; padding:3px 8px; border-radius:4px;">
                                                        {{ $modeLabel }}
                                                    </span>
                                                </td>
                                                <td class="py-12 text-secondary-light" style="font-size:11px; font-family:monospace;">
                                                    {{ $p->reference ?? '—' }}
                                                </td>
                                                <td class="py-12 pe-20 text-end fw-bold" style="color:#8B1A1A;">
                                                    {{ number_format($p->montant, 0, ',', ' ') }} F
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot style="background:#faf7f2;">
                                        <tr>
                                            <td colspan="4" class="px-20 py-12 fw-bold text-primary-light">Total</td>
                                            <td class="py-12 pe-20 text-end fw-bold" style="color:#C8922A; font-size:15px;">
                                                {{ number_format($deja_paye, 0, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── Formulaire de nouveau paiement ── --}}
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-10 py-16 px-20"
                         style="border-bottom:1px solid rgba(0,0,0,.06);">
                        <span class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center"
                              style="background:rgba(139,26,26,.1);">
                            <i class="ri-money-dollar-circle-line text-sm" style="color:#8B1A1A;"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold text-primary-light">3. Enregistrer le paiement</h6>
                    </div>
                    <div class="card-body p-24">

                        @if($errors->any())
                            <div class="alert mb-20 radius-8"
                                 style="background:#ffeaea; color:#8B1A1A; border-left:4px solid #8B1A1A;">
                                <ul class="mb-0 ps-16">
                                    @foreach($errors->all() as $error)
                                        <li style="font-size:13px;">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.paiements.store') }}" autocomplete="off">
                            @csrf
                            <input type="hidden" name="classe_annee_id" value="{{ $classeAnneeId }}">
                            <input type="hidden" name="eleve_id" value="{{ $eleve->id }}">

                            <div class="row gy-20">

                                {{-- Tranche --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary-light"
                                           style="font-size:11px; letter-spacing:1px; text-transform:uppercase;">
                                        Tranche concernée
                                    </label>
                                    <select name="tranche_id"
                                            class="form-select @error('tranche_id') is-invalid @enderror">
                                        <option value="">— Paiement libre —</option>
                                        @foreach($tranches as $t)
                                            <option value="{{ $t->id }}"
                                                    {{ old('tranche_id') == $t->id ? 'selected' : '' }}>
                                                {{ $t->ordre }}. {{ $t->libelle }}
                                                — {{ number_format($t->montant, 0, ',', ' ') }} F
                                                (échéance {{ \Carbon\Carbon::parse($t->date_echeance)->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tranche_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Montant --}}
                                <div class="col-md-6">
                                    <label for="montant"
                                           class="form-label fw-semibold text-secondary-light"
                                           style="font-size:11px; letter-spacing:1px; text-transform:uppercase;">
                                        Montant payé (FCFA) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               id="montant" name="montant"
                                               step="500" min="100"
                                               class="form-control @error('montant') is-invalid @enderror"
                                               placeholder="Ex : 60000"
                                               value="{{ old('montant', $reste > 0 ? $reste : '') }}"
                                               required>
                                        <span class="input-group-text text-secondary-light"
                                              style="font-size:12px;">FCFA</span>
                                        @error('montant')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @if($reste > 0)
                                        <small class="text-secondary-light mt-4 d-block" style="font-size:11px;">
                                            Reste dû : <strong style="color:#8B1A1A;">
                                                {{ number_format($reste, 0, ',', ' ') }} FCFA
                                            </strong>
                                        </small>
                                    @endif
                                </div>

                                {{-- Date de paiement --}}
                                <div class="col-md-6">
                                    <label for="date_paiement"
                                           class="form-label fw-semibold text-secondary-light"
                                           style="font-size:11px; letter-spacing:1px; text-transform:uppercase;">
                                        Date de paiement <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           id="date_paiement" name="date_paiement"
                                           class="form-control @error('date_paiement') is-invalid @enderror"
                                           value="{{ old('date_paiement', date('Y-m-d')) }}"
                                           required>
                                    @error('date_paiement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mode de paiement --}}
                                <div class="col-md-6">
                                    <label for="mode_paiement"
                                           class="form-label fw-semibold text-secondary-light"
                                           style="font-size:11px; letter-spacing:1px; text-transform:uppercase;">
                                        Mode de paiement <span class="text-danger">*</span>
                                    </label>
                                    <select id="mode_paiement" name="mode_paiement"
                                            class="form-select @error('mode_paiement') is-invalid @enderror"
                                            required>
                                        <option value="">— Choisir —</option>
                                        @foreach([
                                            'especes'      => 'Espèces',
                                            'mobile_money' => 'Mobile Money',
                                            'virement'     => 'Virement bancaire',
                                            'carte'        => 'Carte bancaire',
                                            'cheque'       => 'Chèque',
                                        ] as $val => $label)
                                            <option value="{{ $val }}"
                                                    {{ old('mode_paiement', 'especes') === $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('mode_paiement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Référence --}}
                                <div class="col-md-6">
                                    <label for="reference"
                                           class="form-label fw-semibold text-secondary-light"
                                           style="font-size:11px; letter-spacing:1px; text-transform:uppercase;">
                                        Référence / N° reçu
                                        <span class="text-secondary-light fw-normal">(optionnel)</span>
                                    </label>
                                    <input type="text"
                                           id="reference" name="reference"
                                           class="form-control @error('reference') is-invalid @enderror"
                                           placeholder="Ex : PAI-2025-001"
                                           value="{{ old('reference') }}"
                                           maxlength="50">
                                    <small class="text-secondary-light mt-4 d-block" style="font-size:11px;">
                                        Laissez vide pour une référence automatique.
                                    </small>
                                    @error('reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Commentaire --}}
                                <div class="col-md-6">
                                    <label for="commentaire"
                                           class="form-label fw-semibold text-secondary-light"
                                           style="font-size:11px; letter-spacing:1px; text-transform:uppercase;">
                                        Commentaire
                                        <span class="text-secondary-light fw-normal">(optionnel)</span>
                                    </label>
                                    <textarea id="commentaire" name="commentaire"
                                              class="form-control @error('commentaire') is-invalid @enderror"
                                              rows="2"
                                              placeholder="Observations, notes…"
                                              maxlength="500">{{ old('commentaire') }}</textarea>
                                    @error('commentaire')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Boutons --}}
                                <div class="col-12 d-flex align-items-center gap-12 pt-8"
                                     style="border-top:1px solid rgba(0,0,0,.06); margin-top:8px;">
                                    <button type="submit"
                                            class="btn d-flex align-items-center gap-8"
                                            style="background:#8B1A1A; color:#fff; font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; padding:10px 24px; border-radius:3px;">
                                        <i class="ri-save-line"></i>
                                        Enregistrer le paiement
                                    </button>
                                    <a href="{{ route('admin.paiements.create', ['classe_annee_id' => $classeAnneeId]) }}"
                                       class="btn btn-outline-secondary"
                                       style="font-size:12px; padding:10px 20px; border-radius:3px;">
                                        Annuler
                                    </a>
                                </div>

                            </div>{{-- /row --}}
                        </form>
                    </div>
                </div>

            @elseif($classeAnneeId && !$eleveId)

                {{-- Invite à choisir un élève --}}
                <div class="d-flex flex-column align-items-center justify-content-center"
                     style="min-height:360px; opacity:.55;">
                    <i class="ri-user-search-line" style="font-size:3rem; color:#C8922A;"></i>
                    <p class="mt-16 text-secondary-light" style="font-size:14px;">
                        Sélectionnez un élève dans la liste à gauche.
                    </p>
                </div>

            @else

                {{-- Invite à choisir une classe --}}
                <div class="d-flex flex-column align-items-center justify-content-center"
                     style="min-height:360px; opacity:.45;">
                    <i class="ri-building-line" style="font-size:3rem; color:#8B1A1A;"></i>
                    <p class="mt-16 text-secondary-light" style="font-size:14px;">
                        Commencez par sélectionner une classe dans la colonne à gauche.
                    </p>
                </div>

            @endif

        </div>{{-- /col droite --}}
    </div>{{-- /row --}}
</div>
@endsection

@push('scripts')
<script>
    // Remplir automatiquement le montant selon la tranche sélectionnée
    (function () {
        const selTranche = document.querySelector('select[name="tranche_id"]');
        const inputMontant = document.getElementById('montant');
        if (!selTranche || !inputMontant) return;

        const montants = {
            @foreach($tranches as $t)
                '{{ $t->id }}': {{ (float) $t->montant }},
            @endforeach
        };

        selTranche.addEventListener('change', function () {
            const v = montants[this.value];
            if (v) inputMontant.value = v;
        });
    })();
</script>
@endpush