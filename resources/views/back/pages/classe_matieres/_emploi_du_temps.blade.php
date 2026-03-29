{{-- ════════════════════════════════════════════════════════════════
     EMPLOI DU TEMPS — positionnement absolu, tous les jours
     À coller juste avant @endsection dans classe_matieres/index
     ════════════════════════════════════════════════════════════════ --}}

@php
    $edtH0     = 7;
    $edtH1     = 19;
    $edtSlotH  = 44;   // px par heure
    $edtLabelW = 52;   // px colonne des heures
    $edtTotalH = ($edtH1 - $edtH0) * $edtSlotH;

    $edtJours  = [0=>'Lundi',1=>'Mardi',2=>'Mercredi',3=>'Jeudi',4=>'Vendredi',5=>'Samedi'];

    // Palette 8 couleurs (classes CSS définies ci-dessous)
    $edtPalettes = ['cp','ct','cc','cb','ca','ck','cg','cr'];

    // Construire la map matière → classe couleur
    $edtMatieres = $seances->flatten()->pluck('matiere.nom_matiere')->unique()->values();
    $edtColorMap = [];
    foreach ($edtMatieres as $i => $nom) {
        $edtColorMap[$nom] = $edtPalettes[$i % count($edtPalettes)];
    }

    // Toutes les séances à plat, indexées par jour
    $edtParJour = [];
    foreach ($edtJours as $idx => $nomJour) {
        $edtParJour[$idx] = $seances->flatten()
            ->filter(fn($s) => $s->jour_semaine == $idx)
            ->values();
    }
@endphp

@if($seances->isNotEmpty())
<div class="card mt-4">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0">
            <i class="ri-calendar-2-line me-2 text-primary-600"></i>
            Emploi du temps
            <span class="fw-normal text-secondary-light fs-6 ms-1">
                {{ $classeAnnee->classe->full_name ?? '' }} — {{ $classeAnnee->anneeScolaire->libelle ?? '' }}
            </span>
        </h5>
    </div>

    <div class="card-body pb-3">

        {{-- Légende --}}
        <div class="d-flex flex-wrap gap-2 mb-3">
            @foreach($edtMatieres as $nom)
            <span class="edt-pill edt-{{ $edtColorMap[$nom] }}">{{ $nom }}</span>
            @endforeach
        </div>

        {{-- Grille scrollable --}}
        <div style="overflow-x:auto;">
            <div class="edt-table" style="
                min-width:{{ $edtLabelW + 6 * 120 }}px;
                grid-template-columns: {{ $edtLabelW }}px repeat(6, 1fr);
            ">

                {{-- ── En-tête ── --}}
                <div class="edt-corner"></div>
                @foreach($edtJours as $idx => $nomJour)
                <div class="edt-th">{{ $nomJour }}</div>
                @endforeach

                {{-- ── Corps : colonne heures + 6 colonnes jours ── --}}
                {{-- On utilise une ligne de séparation : une seule cellule "body"
                     qui couvre toute la largeur via position relative --}}
                <div class="edt-body-wrap" style="grid-column: 1 / span 7; height:{{ $edtTotalH }}px; position:relative;">

                    {{-- Colonne des heures --}}
                    <div style="position:absolute;top:0;left:0;width:{{ $edtLabelW }}px;height:100%;">
                        @for($h = $edtH0; $h < $edtH1; $h++)
                        <div class="edt-hour-lbl" style="height:{{ $edtSlotH }}px;">{{ $h }}h</div>
                        @endfor
                    </div>

                    {{-- 6 colonnes jours --}}
                    @foreach($edtJours as $jIdx => $nomJour)
                    @php
                        $colLeft = $edtLabelW + $jIdx * 0; // calculé en CSS avec flex/grid
                    @endphp
                    <div class="edt-day-col" style="
                        position:absolute;
                        top:0; bottom:0;
                        left:calc({{ $edtLabelW }}px + {{ $jIdx }} * ((100% - {{ $edtLabelW }}px) / 6));
                        width:calc((100% - {{ $edtLabelW }}px) / 6);
                    ">
                        {{-- Lignes horizontales --}}
                        @for($h = 0; $h < ($edtH1 - $edtH0); $h++)
                        <div style="
                            position:absolute;
                            top:{{ $h * $edtSlotH }}px;
                            left:0;right:0;
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
                            $cls     = $edtColorMap[$seance->matiere->nom_matiere] ?? 'ct';
                            $timeStr = \Carbon\Carbon::parse($seance->heure_debut)->format('H\hi')
                                     . ' – '
                                     . \Carbon\Carbon::parse($seance->heure_fin)->format('H\hi');
                        @endphp
                        <div class="edt-block edt-{{ $cls }}"
                             style="position:absolute;top:{{ $topPx }}px;height:{{ $hautPx }}px;left:3px;right:3px;">
                            <div class="edt-block-mat">{{ $seance->matiere->nom_matiere }}</div>
                            @if($seance->enseignant)
                            <div class="edt-block-ens">
                                <i class="ri-user-line" style="font-size:9px;"></i>
                                {{ $seance->enseignant->nom_complet }}
                            </div>
                            @endif
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

{{-- ── Styles emploi du temps ──────────────────────────────── --}}
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
.edt-day-col {
    border-right: 0.5px solid #e5e7eb;
}
.edt-day-col:last-child { border-right: none; }

/* Blocs matière */
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

/* Légende pills */
.edt-pill { font-size: 12px; font-weight: 500; padding: 3px 12px; border-radius: 20px; }

/* Palette couleurs */
.edt-cp { background:#EEEDFE; color:#3C3489; border-left-color:#7F77DD; }
.edt-ct { background:#E1F5EE; color:#085041; border-left-color:#1D9E75; }
.edt-cc { background:#FAECE7; color:#712B13; border-left-color:#D85A30; }
.edt-cb { background:#E6F1FB; color:#0C447C; border-left-color:#378ADD; }
.edt-ca { background:#FAEEDA; color:#633806; border-left-color:#BA7517; }
.edt-ck { background:#FBEAF0; color:#72243E; border-left-color:#D4537E; }
.edt-cg { background:#EAF3DE; color:#27500A; border-left-color:#639922; }
.edt-cr { background:#FCEBEB; color:#791F1F; border-left-color:#E24B4A; }
</style>
