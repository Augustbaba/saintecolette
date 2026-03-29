@extends('back.layouts.master')

@section('content')
<div class="dashboard-main-body">

    {{-- Breadcrumb --}}
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">
                Emploi du temps — {{ $enseignant->nom_complet }}
            </h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ <a href="{{ route('admin.enseignants.index') }}" class="text-secondary-light hover-text-primary hover-underline">Enseignants</a></span>
                <span class="text-secondary-light">/ Emploi du temps</span>
            </div>
        </div>
        <a href="{{ route('admin.enseignants.edit', $enseignant) }}" class="btn btn-outline-primary d-flex align-items-center gap-6">
            <i class="ri-edit-line"></i> Modifier le profil
        </a>
    </div>

    {{-- Carte profil enseignant --}}
    <div class="card mb-24">
        <div class="card-body d-flex align-items-center gap-4 flex-wrap">
            <div class="bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4"
                 style="width:64px;height:64px;min-width:64px;">
                {{ strtoupper(substr($enseignant->prenom, 0, 1)) }}{{ strtoupper(substr($enseignant->nom, 0, 1)) }}
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-1 fw-semibold">{{ $enseignant->nom_complet }}</h5>
                <div class="text-secondary-light d-flex flex-wrap gap-3" style="font-size:13px;">
                    <span><i class="ri-mail-line me-1"></i>{{ $enseignant->user->email }}</span>
                    @if($enseignant->telephone)
                    <span><i class="ri-phone-line me-1"></i>{{ $enseignant->telephone }}</span>
                    @endif
                    @if($enseignant->matiere_principale)
                    <span><i class="ri-book-2-line me-1"></i>{{ $enseignant->matiere_principale }}</span>
                    @endif
                </div>
            </div>
            <div class="text-center px-3">
                <div class="fw-bold fs-4 text-primary-600">{{ $enseignant->seances->count() }}</div>
                <div class="text-secondary-light" style="font-size:12px;">séance(s)/sem.</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         EMPLOI DU TEMPS GRILLE — même logique que la classe
    ══════════════════════════════════════════════════════ --}}
    @php
        $edtH0     = 7;
        $edtH1     = 19;
        $edtSlotH  = 44;
        $edtLabelW = 52;
        $edtTotalH = ($edtH1 - $edtH0) * $edtSlotH;
        $edtJours  = [0=>'Lundi',1=>'Mardi',2=>'Mercredi',3=>'Jeudi',4=>'Vendredi',5=>'Samedi'];
        $edtPalettes = ['cp','ct','cc','cb','ca','ck','cg','cr'];

        // Pour l'enseignant, la couleur encode la CLASSE
        // → on distingue visuellement dans quelle classe il intervient
        $edtClasses = $enseignant->seances
            ->map(fn($s) => ($s->classeAnnee->classe->full_name ?? '?')
                          . ' (' . ($s->classeAnnee->anneeScolaire->libelle ?? '') . ')')
            ->unique()->values();

        $edtColorMap = [];
        foreach ($edtClasses as $i => $label) {
            $edtColorMap[$label] = $edtPalettes[$i % count($edtPalettes)];
        }

        // Séances indexées par jour
        $edtParJour = [];
        foreach ($edtJours as $idx => $nomJour) {
            $edtParJour[$idx] = $enseignant->seances
                ->filter(fn($s) => $s->jour_semaine == $idx)
                ->sortBy('heure_debut')
                ->values();
        }
    @endphp

    @if($enseignant->seances->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 text-secondary-light">
                <i class="ri-calendar-line ri-3x mb-3 d-block"></i>
                Aucune séance planifiée pour cet enseignant.
            </div>
        </div>
    @else
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="mb-0">
                <i class="ri-calendar-2-line me-2 text-primary-600"></i>
                Planning hebdomadaire
            </h5>
        </div>
        <div class="card-body pb-3">

            {{-- Légende : une couleur par classe --}}
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($edtClasses as $label)
                <span class="edt-pill edt-{{ $edtColorMap[$label] }}">{{ $label }}</span>
                @endforeach
            </div>

            <div style="overflow-x:auto;">
                <div class="edt-table" style="
                    min-width:{{ $edtLabelW + 6 * 120 }}px;
                    grid-template-columns: {{ $edtLabelW }}px repeat(6, 1fr);
                ">
                    {{-- En-tête --}}
                    <div class="edt-corner"></div>
                    @foreach($edtJours as $idx => $nomJour)
                    <div class="edt-th">{{ $nomJour }}</div>
                    @endforeach

                    {{-- Corps --}}
                    <div class="edt-body-wrap"
                         style="grid-column: 1 / span 7; height:{{ $edtTotalH }}px; position:relative;">

                        {{-- Labels heures --}}
                        <div style="position:absolute;top:0;left:0;width:{{ $edtLabelW }}px;height:100%;">
                            @for($h = $edtH0; $h < $edtH1; $h++)
                            <div class="edt-hour-lbl" style="height:{{ $edtSlotH }}px;">{{ $h }}h</div>
                            @endfor
                        </div>

                        {{-- 6 colonnes jours --}}
                        @foreach($edtJours as $jIdx => $nomJour)
                        <div class="edt-day-col" style="
                            position:absolute;
                            top:0; bottom:0;
                            left:calc({{ $edtLabelW }}px + {{ $jIdx }} * ((100% - {{ $edtLabelW }}px) / 6));
                            width:calc((100% - {{ $edtLabelW }}px) / 6);
                        ">
                            {{-- Lignes horizontales fond --}}
                            @for($h = 0; $h < ($edtH1 - $edtH0); $h++)
                            <div style="
                                position:absolute;
                                top:{{ $h * $edtSlotH }}px;
                                left:0; right:0;
                                border-top:0.5px solid {{ $h === 0 ? '#dee2e6' : '#f0f0f0' }};
                            "></div>
                            @endfor

                            {{-- Séances du jour --}}
                            @foreach($edtParJour[$jIdx] as $seance)
                            @php
                                [$debH, $debM] = explode(':', \Carbon\Carbon::parse($seance->heure_debut)->format('H:i'));
                                [$finH, $finM] = explode(':', \Carbon\Carbon::parse($seance->heure_fin)->format('H:i'));
                                $debMin  = ($debH - $edtH0) * 60 + $debM;
                                $finMin  = ($finH - $edtH0) * 60 + $finM;
                                $topPx   = round($debMin / 60 * $edtSlotH) + 3;
                                $hautPx  = round(($finMin - $debMin) / 60 * $edtSlotH) - 6;
                                $classeLabel = ($seance->classeAnnee->classe->full_name ?? '?')
                                             . ' (' . ($seance->classeAnnee->anneeScolaire->libelle ?? '') . ')';
                                $cls     = $edtColorMap[$classeLabel] ?? 'ct';
                                $timeStr = \Carbon\Carbon::parse($seance->heure_debut)->format('H\hi')
                                         . ' – '
                                         . \Carbon\Carbon::parse($seance->heure_fin)->format('H\hi');
                            @endphp
                            <div class="edt-block edt-{{ $cls }}"
                                 style="position:absolute;top:{{ $topPx }}px;height:{{ $hautPx }}px;left:3px;right:3px;">
                                <div class="edt-block-mat">{{ $seance->matiere->nom_matiere }}</div>
                                <div class="edt-block-ens">
                                    <i class="ri-school-line" style="font-size:9px;"></i>
                                    {{ $seance->classeAnnee->classe->full_name ?? '—' }}
                                </div>
                                <div class="edt-block-time">{{ $timeStr }}</div>
                            </div>
                            @endforeach

                        </div>
                        @endforeach

                    </div>{{-- fin edt-body-wrap --}}
                </div>{{-- fin edt-table --}}
            </div>{{-- fin overflow-x --}}

        </div>
    </div>
    @endif

</div>
@endsection

<style>
.edt-table {
    display: grid;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
}
.edt-corner {
    background: #f8f9fa;
    border-right: 1px solid #dee2e6;
    border-bottom: 2px solid #dee2e6;
}
.edt-th {
    background: #f8f9fa;
    border-right: 0.5px solid #dee2e6;
    border-bottom: 2px solid #dee2e6;
    padding: 10px 6px;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
}
.edt-th:last-child { border-right: none; }
.edt-body-wrap { background: #fff; }
.edt-hour-lbl {
    border-right: 1px solid #dee2e6;
    border-bottom: 0.5px solid #f0f0f0;
    background: #f8f9fa;
    padding: 4px 6px 0;
    font-size: 11px;
    color: #9ca3af;
    text-align: right;
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    box-sizing: border-box;
}
.edt-day-col { border-right: 0.5px solid #e5e7eb; }
.edt-day-col:last-child { border-right: none; }
.edt-block {
    border-radius: 6px;
    padding: 4px 7px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
    box-sizing: border-box;
    border-left-width: 3px;
    border-left-style: solid;
    transition: opacity .15s;
}
.edt-block:hover { opacity: .85; }
.edt-block-mat  { font-size: 11px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.3; }
.edt-block-ens  { font-size: 10px; opacity: .8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
.edt-block-time { font-size: 9px;  opacity: .6; margin-top: 2px; }
.edt-pill { font-size: 12px; font-weight: 500; padding: 3px 12px; border-radius: 20px; }
.edt-cp { background:#EEEDFE; color:#3C3489; border-left-color:#7F77DD; }
.edt-ct { background:#E1F5EE; color:#085041; border-left-color:#1D9E75; }
.edt-cc { background:#FAECE7; color:#712B13; border-left-color:#D85A30; }
.edt-cb { background:#E6F1FB; color:#0C447C; border-left-color:#378ADD; }
.edt-ca { background:#FAEEDA; color:#633806; border-left-color:#BA7517; }
.edt-ck { background:#FBEAF0; color:#72243E; border-left-color:#D4537E; }
.edt-cg { background:#EAF3DE; color:#27500A; border-left-color:#639922; }
.edt-cr { background:#FCEBEB; color:#791F1F; border-left-color:#E24B4A; }
</style>
