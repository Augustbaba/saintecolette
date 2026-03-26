<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Eleve;
use App\Models\Scolarite;
use App\Models\Tranche;
use App\Models\ClasseAnnee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaiementController extends Controller
{
    /* ══════════════════════════════════════════════════════
     *  HISTORIQUE — tous les paiements enregistrés
     * ══════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        $classesAnnees = ClasseAnnee::with(['classe.niveau', 'anneeScolaire'])->get();

        // ── Requête filtrée ──
        $q = Paiement::with([
                'eleve.classeAnnee.classe.niveau',
                'tranche',
            ])
            ->orderByDesc('date_paiement');

        if ($request->filled('classe_annee_id')) {
            $q->whereHas('eleve', fn($e) =>
                $e->where('classe_annee_id', $request->classe_annee_id)
            );
        }

        if ($request->filled('mode_paiement')) {
            $q->where('mode_paiement', $request->mode_paiement);
        }

        if ($request->filled('date_debut')) {
            $q->where('date_paiement', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $q->where('date_paiement', '<=', $request->date_fin);
        }

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $q->whereHas('eleve', fn($e) =>
                $e->where('nom', 'like', $s)
                  ->orWhere('prenom', 'like', $s)
                  ->orWhere('matricule', 'like', $s)
            );
        }

        $paiements = $q->paginate(30);

        // ── KPI globaux (sans filtre de pagination) ──
        $stats = [
            'total_encaisse'   => Paiement::sum('montant'),
            'nb_paiements'     => Paiement::count(),
            'eleves_a_jour'    => $this->elevesAJour(),
            'eleves_debiteurs' => $this->nbDebiteurs(),
        ];

        return view('back.pages.paiements.index', compact(
            'paiements', 'classesAnnees', 'stats'
        ));
    }

    /* ══════════════════════════════════════════════════════
     *  DÉBITEURS — élèves avec solde impayé
     * ══════════════════════════════════════════════════════ */
    public function debiteurs(Request $request)
    {
        $classesAnnees = ClasseAnnee::with(['classe.niveau', 'anneeScolaire'])
            ->whereHas('scolarite')
            ->get();

        // ── Requête principale via DB::table pour performance ──
        $q = DB::table('eleves as e')
            ->join('classe_annees as ca',   'ca.id', '=', 'e.classe_annee_id')
            ->join('classes as cl',         'cl.id', '=', 'ca.classe_id')
            ->join('niveaux as nv',         'nv.id', '=', 'cl.niveau_id')
            ->join('scolarites as sc',      'sc.classe_annee_id', '=', 'ca.id')
            ->join('parents as p',          'p.id',  '=', 'e.parent_id')
            ->leftJoin(
                DB::raw('(SELECT eleve_id, SUM(montant) as total_paye FROM paiements GROUP BY eleve_id) as pm'),
                'pm.eleve_id', '=', 'e.id'
            )
            ->select([
                'e.id          as eleve_id',
                'e.matricule',
                'e.nom',
                'e.prenom',
                'e.classe_annee_id',
                'nv.nom        as niveau_nom',
                'cl.suffixe    as classe_suffixe',
                'sc.montant_annuel',
                DB::raw('COALESCE(pm.total_paye, 0) as total_paye'),
                DB::raw('(sc.montant_annuel - COALESCE(pm.total_paye, 0)) as reste'),
                'p.nom         as parent_nom',
                'p.prenom      as parent_prenom',
                'p.telephone   as parent_telephone',
                'p.whatsapp    as parent_whatsapp',
            ])
            ->where('e.statut', 'actif')
            // N'afficher que ceux qui ont encore une dette > 0
            ->whereRaw('sc.montant_annuel > COALESCE(pm.total_paye, 0)')
            ->orderByDesc('reste');

        // ── Filtres ──
        if ($request->filled('classe_annee_id')) {
            $q->where('e.classe_annee_id', $request->classe_annee_id);
        }

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $q->where(function ($sub) use ($s) {
                $sub->where('e.nom',         'like', $s)
                    ->orWhere('e.prenom',     'like', $s)
                    ->orWhere('e.matricule',  'like', $s)
                    ->orWhere('p.nom',        'like', $s)
                    ->orWhere('p.prenom',     'like', $s);
            });
        }

        if ($request->filled('niveau_dette')) {
            switch ($request->niveau_dette) {
                case 'critique':
                    // Payé < 25 % du montant annuel
                    $q->whereRaw('COALESCE(pm.total_paye,0) / sc.montant_annuel < 0.25');
                    break;
                case 'eleve':
                    $q->whereRaw('COALESCE(pm.total_paye,0) / sc.montant_annuel BETWEEN 0.25 AND 0.50');
                    break;
                case 'partiel':
                    $q->whereRaw('COALESCE(pm.total_paye,0) / sc.montant_annuel BETWEEN 0.50 AND 0.75');
                    break;
            }
        }

        // Total des dettes (avant pagination)
        $totalDu         = (clone $q)->sum(DB::raw('sc.montant_annuel - COALESCE(pm.total_paye,0)'));
        $totalScolarites = (clone $q)->sum('sc.montant_annuel');

        $debiteurs = $q->paginate(25);

        return view('back.pages.paiements.debiteurs', compact(
            'debiteurs',
            'classesAnnees',
            'totalDu',
            'totalScolarites'
        ));
    }

    /* ══════════════════════════════════════════════════════
     *  CREATE — sélection classe + élève
     * ══════════════════════════════════════════════════════ */
    public function create(Request $request)
    {
        $classesAnnees = ClasseAnnee::with(['classe.niveau', 'anneeScolaire', 'scolarite.tranches'])
            ->whereHas('scolarite')
            ->orderBy('id')
            ->get();

        $classeAnneeId = $request->input('classe_annee_id');
        $eleveId       = $request->input('eleve_id');

        $eleves    = collect();
        $eleve     = null;
        $scolarite = null;
        $tranches  = collect();
        $deja_paye = 0;

        if ($classeAnneeId) {
            $eleves = Eleve::where('classe_annee_id', $classeAnneeId)
                ->where('statut', 'actif')
                ->orderBy('nom')
                ->get();

            $scolarite = Scolarite::with('tranches')
                ->where('classe_annee_id', $classeAnneeId)
                ->first();

            if ($scolarite) {
                $tranches = $scolarite->tranches()->orderBy('ordre')->get();
            }
        }

        if ($eleveId) {
            $eleve     = Eleve::with('parent')->find($eleveId);
            $deja_paye = Paiement::where('eleve_id', $eleveId)->sum('montant');
        }

        return view('back.pages.paiements.create', compact(
            'classesAnnees', 'classeAnneeId',
            'eleves', 'eleveId',
            'eleve', 'scolarite', 'tranches', 'deja_paye'
        ));
    }

    /* ══════════════════════════════════════════════════════
     *  STORE — enregistrement
     * ══════════════════════════════════════════════════════ */
    public function store(Request $request)
    {
        $request->validate([
            'eleve_id'      => 'required|exists:eleves,id',
            'tranche_id'    => 'nullable|exists:tranches,id',
            'montant'       => 'required|numeric|min:100',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|in:especes,mobile_money,virement,carte,cheque',
            'reference'     => 'nullable|string|max:50',
            'commentaire'   => 'nullable|string|max:500',
        ]);

        $eleve = Eleve::findOrFail($request->eleve_id);

        Paiement::create([
            'eleve_id'      => $eleve->id,
            'parent_id'     => $eleve->parent_id,
            'tranche_id'    => $request->tranche_id ?: null,
            'montant'       => $request->montant,
            'date_paiement' => $request->date_paiement,
            'mode_paiement' => $request->mode_paiement,
            'reference'     => $request->reference ?: Str::upper(Str::random(8)),
            'commentaire'   => $request->commentaire,
        ]);

        return redirect()
            ->route('admin.paiements.create', [
                'classe_annee_id' => $request->classe_annee_id,
                'eleve_id'        => $eleve->id,
            ])
            ->with('success', 'Paiement enregistré avec succès.');
    }

    /* ── Helpers privés ── */

    private function elevesAJour(): int
    {
        return DB::table('eleves as e')
            ->join('scolarites as sc',  'sc.classe_annee_id', '=', 'e.classe_annee_id')
            ->leftJoin(
                DB::raw('(SELECT eleve_id, SUM(montant) as tp FROM paiements GROUP BY eleve_id) as pm'),
                'pm.eleve_id', '=', 'e.id'
            )
            ->where('e.statut', 'actif')
            ->whereRaw('COALESCE(pm.tp, 0) >= sc.montant_annuel')
            ->count();
    }

    private function nbDebiteurs(): int
    {
        return DB::table('eleves as e')
            ->join('scolarites as sc',  'sc.classe_annee_id', '=', 'e.classe_annee_id')
            ->leftJoin(
                DB::raw('(SELECT eleve_id, SUM(montant) as tp FROM paiements GROUP BY eleve_id) as pm2'),
                'pm2.eleve_id', '=', 'e.id'
            )
            ->where('e.statut', 'actif')
            ->whereRaw('sc.montant_annuel > COALESCE(pm2.tp, 0)')
            ->count();
    }
}