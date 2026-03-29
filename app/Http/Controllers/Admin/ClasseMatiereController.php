<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClasseAnnee;
use App\Models\Matiere;
use App\Models\ClasseMatiere;
use App\Models\Enseignant;
use App\Models\Seance;
use Illuminate\Http\Request;

class ClasseMatiereController extends Controller
{
    // ─── Détection de chevauchement (bornes STRICTES) ────────────────────────
    //
    // Deux créneaux [A_debut, A_fin] et [B_debut, B_fin] se chevauchent si :
    //   A_debut < B_fin  ET  A_fin > B_debut
    //
    // Ainsi 08:00-10:00 et 10:00-12:00 ne se chevauchent PAS (10:00 < 10:00 = false).
    //
    private function queryConflit($builder, string $debut, string $fin)
    {
        return $builder->where('heure_debut', '<', $fin)
                       ->where('heure_fin',   '>', $debut);
    }

    // ─── INDEX ────────────────────────────────────────────────────────────────

    public function index(ClasseAnnee $classeAnnee)
    {
        $matieres    = Matiere::orderBy('nom_matiere')->get();
        $enseignants = Enseignant::with('user')->orderBy('nom')->get();

        $classeMatieres = ClasseMatiere::with(['matiere', 'enseignant.user'])
            ->where('classe_annee_id', $classeAnnee->id)
            ->get();

        $seances = Seance::with(['matiere', 'enseignant.user'])
            ->where('classe_annee_id', $classeAnnee->id)
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('matiere_id');

        return view('back.pages.classe_matieres.index', compact(
            'classeAnnee', 'matieres', 'enseignants', 'classeMatieres', 'seances'
        ));
    }

    // ─── STORE MATIÈRE ────────────────────────────────────────────────────────

    public function store(Request $request, ClasseAnnee $classeAnnee)
    {
        $request->validate([
            'matiere_id'    => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'coefficient'   => 'required|numeric|min:0.1|max:10',
        ]);

        $exists = ClasseMatiere::where('classe_annee_id', $classeAnnee->id)
                               ->where('matiere_id', $request->matiere_id)
                               ->exists();
        if ($exists) {
            return back()->with('error', 'Cette matière est déjà associée à cette classe.');
        }

        ClasseMatiere::create([
            'classe_annee_id' => $classeAnnee->id,
            'matiere_id'      => $request->matiere_id,
            'enseignant_id'   => $request->enseignant_id,
            'coefficient'     => $request->coefficient,
        ]);

        return back()->with('success', 'Matière ajoutée avec succès.');
    }

    // ─── UPDATE MATIÈRE ───────────────────────────────────────────────────────

    public function update(Request $request, ClasseAnnee $classeAnnee, Matiere $matiere)
    {
        $request->validate([
            'coefficient'   => 'required|numeric|min:0.1|max:10',
            'enseignant_id' => 'required|exists:enseignants,id',
        ]);

        // firstOrFail() retourne un Model Eloquent standard (pas un Pivot)
        // grâce au primaryKey 'id' ajouté par la migration → update/delete fonctionnent
        $association = ClasseMatiere::where('classe_annee_id', $classeAnnee->id)
                                    ->where('matiere_id', $matiere->id)
                                    ->firstOrFail();

        $association->update([
            'coefficient'   => $request->coefficient,
            'enseignant_id' => $request->enseignant_id,
        ]);

        return back()->with('success', 'Matière mise à jour.');
    }

    // ─── DESTROY MATIÈRE ──────────────────────────────────────────────────────

    public function destroy(ClasseAnnee $classeAnnee, Matiere $matiere)
    {
        Seance::where('classe_annee_id', $classeAnnee->id)
              ->where('matiere_id', $matiere->id)
              ->delete();

        ClasseMatiere::where('classe_annee_id', $classeAnnee->id)
                     ->where('matiere_id', $matiere->id)
                     ->firstOrFail()
                     ->delete();

        return back()->with('success', 'Matière et ses séances retirées de la classe.');
    }

    // ─── STORE SÉANCE ─────────────────────────────────────────────────────────

    public function storeSeance(Request $request, ClasseAnnee $classeAnnee, Matiere $matiere)
    {
        $request->validate([
            'jour_semaine' => 'required|integer|between:0,5',
            'heure_debut'  => 'required|date_format:H:i',
            'heure_fin'    => 'required|date_format:H:i|after:heure_debut',
        ]);

        $classeMatiere = ClasseMatiere::where('classe_annee_id', $classeAnnee->id)
                                      ->where('matiere_id', $matiere->id)
                                      ->firstOrFail();

        $debut = $request->heure_debut;
        $fin   = $request->heure_fin;
        $jour  = $request->jour_semaine;

        // Conflit classe
        $conflitClasse = $this->queryConflit(
            Seance::where('classe_annee_id', $classeAnnee->id)->where('jour_semaine', $jour),
            $debut, $fin
        )->exists();

        if ($conflitClasse) {
            return back()->with('error', 'Conflit d\'horaire : la classe a déjà un cours à ce créneau.');
        }

        // Conflit enseignant
        $conflitEnseignant = $this->queryConflit(
            Seance::where('enseignant_id', $classeMatiere->enseignant_id)->where('jour_semaine', $jour),
            $debut, $fin
        )->exists();

        if ($conflitEnseignant) {
            return back()->with('error', 'Conflit d\'horaire : l\'enseignant a déjà un cours à ce créneau.');
        }

        Seance::create([
            'classe_annee_id' => $classeAnnee->id,
            'matiere_id'      => $matiere->id,
            'enseignant_id'   => $classeMatiere->enseignant_id,
            'jour_semaine'    => $jour,
            'heure_debut'     => $debut,
            'heure_fin'       => $fin,
        ]);

        return back()->with('success', 'Séance ajoutée.');
    }

    // ─── UPDATE SÉANCE ────────────────────────────────────────────────────────

    public function updateSeance(Request $request, ClasseAnnee $classeAnnee, Matiere $matiere, Seance $seance)
    {
        $request->validate([
            'jour_semaine' => 'required|integer|between:0,5',
            'heure_debut'  => 'required|date_format:H:i',
            'heure_fin'    => 'required|date_format:H:i|after:heure_debut',
        ]);

        $debut = $request->heure_debut;
        $fin   = $request->heure_fin;
        $jour  = $request->jour_semaine;

        // Conflit classe (hors séance courante)
        $conflitClasse = $this->queryConflit(
            Seance::where('classe_annee_id', $classeAnnee->id)
                  ->where('jour_semaine', $jour)
                  ->where('id', '!=', $seance->id),
            $debut, $fin
        )->exists();

        if ($conflitClasse) {
            return back()->with('error', 'Conflit d\'horaire : la classe a déjà un cours à ce créneau.');
        }

        // Conflit enseignant (hors séance courante)
        $conflitEnseignant = $this->queryConflit(
            Seance::where('enseignant_id', $seance->enseignant_id)
                  ->where('jour_semaine', $jour)
                  ->where('id', '!=', $seance->id),
            $debut, $fin
        )->exists();

        if ($conflitEnseignant) {
            return back()->with('error', 'Conflit d\'horaire : l\'enseignant a déjà un cours à ce créneau.');
        }

        $seance->update([
            'jour_semaine' => $jour,
            'heure_debut'  => $debut,
            'heure_fin'    => $fin,
        ]);

        return back()->with('success', 'Séance modifiée.');
    }

    // ─── DESTROY SÉANCE ───────────────────────────────────────────────────────

    public function destroySeance(ClasseAnnee $classeAnnee, Matiere $matiere, Seance $seance)
    {
        $seance->delete();
        return back()->with('success', 'Séance supprimée.');
    }
}
