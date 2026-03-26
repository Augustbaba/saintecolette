<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Eleve;
use App\Models\ClasseAnnee;
use App\Models\Parents as ParentModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportEleveController extends Controller
{
    /**
     * Formulaire d'upload
     */
    public function create()
    {
        Log::info('=== ImportEleveController@create - Début ===');
        
        $classesAnnees = ClasseAnnee::with('classe.niveau', 'anneeScolaire')->get();
        
        Log::info('Classes trouvées : ' . $classesAnnees->count());
        
        return view('back.pages.eleves.import_create', compact('classesAnnees'));
    }

    /**
     * Traite le fichier et redirige vers la prévisualisation
     */
    public function process(Request $request)
    {
        Log::info('=== ImportEleveController@process - Début ===');
        Log::info('Données reçues :', $request->all());
        
        try {
            $request->validate([
                'classe_annee_id' => 'required|exists:classe_annees,id',
                'file'            => 'required|mimes:xlsx,xls,csv|max:5120',
                'sheet_name'      => 'required|string|max:100',
            ]);
            
            Log::info('Validation OK');
            
        } catch (\Exception $e) {
            Log::error('Erreur de validation : ' . $e->getMessage());
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        $classeAnnee = ClasseAnnee::with('classe.niveau', 'anneeScolaire')
            ->findOrFail($request->classe_annee_id);
        
        Log::info('Classe sélectionnée : ' . $classeAnnee->classe->niveau->nom . ' ' . $classeAnnee->classe->suffixe);
        Log::info('Onglet recherché : ' . $request->sheet_name);

        // Charger le fichier Excel
        $file = $request->file('file');
        Log::info('Fichier reçu : ' . $file->getClientOriginalName() . ' - Taille : ' . $file->getSize() . ' bytes');
        
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            Log::info('Fichier Excel chargé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur chargement Excel : ' . $e->getMessage());
            return redirect()->back()->withErrors(['file' => 'Le fichier Excel est corrompu ou invalide.']);
        }

        // Récupérer la feuille par son nom
        $worksheet = $spreadsheet->getSheetByName($request->sheet_name);
        if (!$worksheet) {
            Log::warning('Onglet non trouvé : ' . $request->sheet_name);
            Log::info('Onglets disponibles : ' . implode(', ', $spreadsheet->getSheetNames()));
            
            return redirect()->back()->withErrors([
                'sheet_name' => "L'onglet '{$request->sheet_name}' n'existe pas dans le fichier. Onglets disponibles : " . implode(', ', $spreadsheet->getSheetNames()),
            ]);
        }
        
        Log::info('Onglet trouvé : ' . $request->sheet_name);

        // Lire les en-têtes (ligne 1)
        $highestColumn = $worksheet->getHighestColumn();
        Log::info('Colonne max : ' . $highestColumn);
        
        $rawHeaders = $worksheet->rangeToArray(
            'A1:' . $highestColumn . '1',
            null, true, false
        )[0];
        $headers = array_map('trim', $rawHeaders);
        
        Log::info('En-têtes trouvés :', $headers);

        // Mapping des colonnes
        $columnMapping = [
            'matricule' => ['matricule'],
            'nom'       => ['nom'],
            'prenom'    => ['prenoms', 'prenom'],
            'classe'    => ['classe'],
            'tel_eleve' => ['n gsm', 'ngsm', 'gsm', 'telephone', 'tel', 'n° gsm', 'numéro', 'numero'],
            'sexe'      => ['sexe', 'genre', 'gender'],
            'date_naissance' => ['date naissance', 'date_naissance', 'birthdate', 'date de naissance', 'naissance'],
        ];

        $normaliser = function (string $s): string {
            $s = mb_strtolower(trim($s), 'UTF-8');
            $from = ['é','è','ê','ë','à','â','ä','î','ï','ô','ö','ù','û','ü','ç','œ','æ'];
            $to   = ['e','e','e','e','a','a','a','i','i','o','o','u','u','u','c','oe','ae'];
            $s    = str_replace($from, $to, $s);
            $s    = preg_replace('/[^a-z0-9 ]/', '', $s);
            return preg_replace('/\s+/', ' ', trim($s));
        };

        $headersNorm = array_map($normaliser, $headers);
        Log::info('En-têtes normalisés :', $headersNorm);
        
        $colIndexes = [];

        foreach ($columnMapping as $key => $variants) {
            $colIndexes[$key] = null;
            foreach ($variants as $label) {
                $idx = array_search($label, $headersNorm);
                if ($idx !== false) {
                    $colIndexes[$key] = $idx;
                    Log::info("Colonne '{$key}' trouvée à l'index {$idx} (label: {$label})");
                    break;
                }
            }
            if ($colIndexes[$key] === null) {
                Log::warning("Colonne '{$key}' non trouvée");
            }
        }

        $allRows = $worksheet->toArray();
        array_shift($allRows); // Supprimer la ligne d'en-tête
        
        Log::info('Nombre total de lignes (hors en-tête) : ' . count($allRows));

        $data   = [];
        $errors = [];
        $matriculeCounter = 0; // Compteur pour les matricules générés
        $currentYear = now()->format('y'); // Année pour les matricules

        foreach ($allRows as $index => $row) {
            $lineNumber = $index + 2;
            
            // Ignorer les lignes entièrement vides
            $nonEmptyValues = array_filter($row, fn($v) => $v !== null && $v !== '');
            if (empty($nonEmptyValues)) {
                Log::info("Ligne {$lineNumber}: ligne vide, ignorée");
                continue;
            }
            
            Log::info("--- Traitement ligne {$lineNumber} ---");
            Log::info('Données brutes :', $row);

            // Extraire les valeurs
            $matricule = $colIndexes['matricule'] !== null
                ? trim((string)($row[$colIndexes['matricule']] ?? ''))
                : '';
            $nom       = $colIndexes['nom'] !== null
                ? trim((string)($row[$colIndexes['nom']] ?? ''))
                : '';
            $prenom    = $colIndexes['prenom'] !== null
                ? trim((string)($row[$colIndexes['prenom']] ?? ''))
                : '';
            $classe    = $colIndexes['classe'] !== null
                ? trim((string)($row[$colIndexes['classe']] ?? ''))
                : '';
            $telEleve  = $colIndexes['tel_eleve'] !== null
                ? $this->normaliserTelephone((string)($row[$colIndexes['tel_eleve']] ?? ''))
                : '';
            $sexe      = $colIndexes['sexe'] !== null
                ? trim((string)($row[$colIndexes['sexe']] ?? ''))
                : null;
            $dateNaissance = $colIndexes['date_naissance'] !== null
                ? $this->parseDate($row[$colIndexes['date_naissance']] ?? '')
                : null;
            
            Log::info("Données extraites - Matricule: '{$matricule}', Nom: '{$nom}', Prénom: '{$prenom}', Téléphone: '{$telEleve}', Sexe: '{$sexe}', Date naiss: '{$dateNaissance}'");

            $rowErrors = [];

            // Validations obligatoires
            if (empty($nom)) {
                $rowErrors[] = "Nom manquant";
                Log::warning("Ligne {$lineNumber}: Nom manquant");
            }
            if (empty($prenom)) {
                $rowErrors[] = "Prénom manquant";
                Log::warning("Ligne {$lineNumber}: Prénom manquant");
            }

            // Validation sexe (optionnel)
            if (!empty($sexe) && !in_array(strtolower($sexe), ['m', 'f', 'masculin', 'féminin', 'homme', 'femme', 'garçon', 'fille'])) {
                $rowErrors[] = "Sexe invalide : '$sexe' (utilisez M/F)";
                Log::warning("Ligne {$lineNumber}: Sexe invalide - '{$sexe}'");
            }

            // Validation date naissance (optionnel)
            if (!empty($dateNaissance) && !$this->validateDate($dateNaissance)) {
                $rowErrors[] = "Date de naissance invalide : '$dateNaissance'";
                Log::warning("Ligne {$lineNumber}: Date naissance invalide - '{$dateNaissance}'");
            }

            // Validation téléphone
            $telAnormal = false;
            if (!empty($telEleve) && !$this->estTelephoneValide($telEleve)) {
                $rowErrors[] = "⚠ N° GSM anormal conservé tel quel : '$telEleve'";
                $telAnormal = true;
                Log::warning("Ligne {$lineNumber}: Téléphone anormal - '{$telEleve}'");
            }

            // Erreurs bloquantes (nom/prénom manquants) → ignorer la ligne
            if (!empty($rowErrors) && (empty($nom) || empty($prenom))) {
                $errors[$lineNumber] = $rowErrors;
                Log::warning("Ligne {$lineNumber}: Erreurs bloquantes, ligne ignorée : " . implode(', ', $rowErrors));
                continue;
            }

            // Erreurs non bloquantes → on les signale mais on continue
            if (!empty($rowErrors)) {
                $errors[$lineNumber] = $rowErrors;
                Log::info("Ligne {$lineNumber}: Avertissements : " . implode(', ', $rowErrors));
            }

            // Vérification du parent
            $parentId     = null;
            $parentStatus = 'Non lié (parent inexistant)';

            if (!empty($telEleve) && !$telAnormal) {
                $parent = ParentModel::where('telephone', $telEleve)->first();
                if ($parent) {
                    $parentId     = $parent->id;
                    $parentStatus = '✓ Parent existant : ' . $parent->nom . ' ' . $parent->prenom;
                    Log::info("Ligne {$lineNumber}: Parent trouvé - ID: {$parentId}");
                } else {
                    $parentId = null;
                    $parentStatus = '⚠ Parent non trouvé - Élève sans parent';
                    Log::info("Ligne {$lineNumber}: Aucun parent trouvé pour le téléphone '{$telEleve}'");
                }
            } elseif ($telAnormal) {
                $parentId = null;
                $parentStatus = '⚠ Non lié (N° GSM anormal)';
                Log::info("Ligne {$lineNumber}: Téléphone anormal, pas de recherche parent");
            }

            // Gestion du matricule
            $matriculeWarning = null;

            if (empty($matricule)) {
                $matriculeCounter++;
                
                // Compter les élèves existants dans la base pour cette classe
                $existingCount = Eleve::where('classe_annee_id', $classeAnnee->id)->count();
                
                // Calculer le prochain numéro
                $nextNumber = $existingCount + $matriculeCounter;
                $numero = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                $matricule = $currentYear . '-01' . $numero;
                
                // Vérifier les doublons avec les élèves déjà existants
                $i = 0;
                while (Eleve::where('matricule', $matricule)->exists()) {
                    $i++;
                    $nextNumber = $existingCount + $matriculeCounter + $i;
                    $numero = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                    $matricule = $currentYear . '-01' . $numero;
                }
                
                $matriculeWarning = "Matricule généré : $matricule";
                Log::info("Ligne {$lineNumber}: Matricule auto-généré - '{$matricule}' (compteur: {$matriculeCounter}, existants: {$existingCount})");
            } else {
                $matricule = $this->formaterMatricule($matricule);
                Log::info("Ligne {$lineNumber}: Matricule formaté - '{$matricule}'");

                // Vérifier doublon en base
                if (Eleve::where('matricule', $matricule)->exists()) {
                    $original         = $matricule;
                    $matricule        = $this->genererMatriculeUnique($matricule);
                    $matriculeWarning = "⚠ Matricule '$original' doublon → remplacé par '$matricule'";
                    Log::warning("Ligne {$lineNumber}: " . $matriculeWarning);
                }
            }

            if ($matriculeWarning) {
                if (!isset($errors[$lineNumber])) {
                    $errors[$lineNumber] = [];
                }
                $errors[$lineNumber][] = $matriculeWarning;
            }

            $sexeStandard = $this->standardiserSexe($sexe);
            Log::info("Ligne {$lineNumber}: Sexe standardisé - '{$sexeStandard}'");

            $data[] = [
                'index'            => $index,
                'line_number'      => $lineNumber,
                'matricule'        => $matricule,
                'nom'              => $nom,
                'prenom'           => $prenom,
                'sexe'             => $sexeStandard,
                'date_naissance'   => $dateNaissance,
                'classe_excel'     => $classe,
                'tel_eleve'        => $telEleve,
                'parent_status'    => $parentStatus,
                '_parent_id'       => $parentId,
                '_classe_annee_id' => $classeAnnee->id,
            ];
        }

        Log::info('=== FIN TRAITEMENT ===');
        Log::info('Total données valides : ' . count($data));
        Log::info('Total erreurs : ' . count($errors));
        
        if (count($errors) > 0) {
            Log::info('Détail des erreurs :', $errors);
        }

        Session::put('import_eleves_data', $data);
        Session::put('import_eleves_errors', $errors);
        Session::put('import_eleves_classe_annee', $classeAnnee->id);

        if (empty($data)) {
            Log::warning('Aucune donnée valide trouvée dans le fichier');
            return redirect()->back()->withErrors(['file' => 'Aucune donnée valide trouvée dans le fichier. Vérifiez que les colonnes Nom et Prénoms sont présentes et remplies.']);
        }

        return redirect()->route('admin.eleves.import.preview');
    }

    /**
     * Affiche la prévisualisation (GET)
     */
    public function preview()
    {
        Log::info('=== ImportEleveController@preview - Début ===');
        
        $data = Session::get('import_eleves_data', []);
        $errors = Session::get('import_eleves_errors', []);
        $classeAnneeId = Session::get('import_eleves_classe_annee');
        
        Log::info('Données en session - data count: ' . count($data) . ', errors count: ' . count($errors));
        
        if (empty($data)) {
            Log::warning('Aucune donnée en session, redirection vers create');
            return redirect()->route('admin.eleves.import.create')
                ->with('error', 'Aucune donnée trouvée. Veuillez importer un fichier.');
        }
        
        $classeAnnee = ClasseAnnee::with('classe.niveau', 'anneeScolaire')
            ->findOrFail($classeAnneeId);
        
        Log::info('Affichage de la prévisualisation pour la classe : ' . $classeAnnee->classe->niveau->nom);
        
        return view('back.pages.eleves.import_preview', compact('data', 'errors', 'classeAnnee'));
    }

    /**
     * Importe les lignes sélectionnées
     */
    public function store(Request $request)
    {
        Log::info('=== ImportEleveController@store - Début ===');
        Log::info('Indices sélectionnés :', $request->input('selected', []));
        
        $selectedIndices = $request->input('selected', []);
        $data            = Session::get('import_eleves_data', []);

        if (empty($selectedIndices)) {
            Log::warning('Aucune ligne sélectionnée');
            return redirect()->back()->with('error', 'Aucune ligne sélectionnée.');
        }

        $imported = 0;
        $failed   = [];

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                if (!in_array($item['index'], $selectedIndices)) {
                    continue;
                }

                Log::info("Import de l'élève : {$item['nom']} {$item['prenom']} - Matricule: {$item['matricule']}");

                $parentId = $item['_parent_id'] ?? null;
                
                if (empty($parentId)) {
                    Log::info("Aucun parent trouvé pour {$item['nom']} {$item['prenom']}, parent_id = NULL");
                } else {
                    Log::info("Parent trouvé : ID {$parentId}");
                }

                $eleve = Eleve::create([
                    'matricule'        => $item['matricule'],
                    'nom'              => $item['nom'],
                    'prenom'           => $item['prenom'],
                    'sexe'             => $item['sexe'] ?? null,
                    'date_naissance'   => $item['date_naissance'] ?? null,
                    'photo'            => null,
                    'classe_annee_id'  => $item['_classe_annee_id'],
                    'parent_id'        => $parentId,
                    'telephone'        => $item['tel_eleve'] ?: null,
                    'date_inscription' => now()->toDateString(),
                    'statut'           => 'actif',
                ]);
                
                $imported++;
                Log::info("Élève importé avec succès : ID {$eleve->id} - {$item['nom']} {$item['prenom']}");
            }

            DB::commit();

            Session::forget(['import_eleves_data', 'import_eleves_errors', 'import_eleves_classe_annee']);

            $message = "$imported élève(s) importé(s) avec succès.";
            if (!empty($failed)) {
                $message .= ' Échecs : ' . implode('; ', $failed);
            }

            Log::info("Import terminé : {$imported} élèves importés");
            
            return redirect()->route('admin.eleves.import.create')->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'import : ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    // =========================================================================
    // MÉTHODES PRIVÉES
    // =========================================================================

    private function parseDate($value): ?string
    {
        if (empty($value)) return null;
        
        $value = trim($value);
        
        try {
            if (is_numeric($value)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            }
            
            $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y', 'd.m.Y', 'Y/m/d'];
            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, $value);
                if ($date && $date->format($format) === $value) {
                    return $date->format('Y-m-d');
                }
            }
            
            $date = Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Erreur parsing date '{$value}' : " . $e->getMessage());
            return null;
        }
    }

    private function validateDate($date): bool
    {
        try {
            Carbon::parse($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function standardiserSexe(?string $sexe): ?string
    {
        if (empty($sexe)) return null;
        
        $sexe = strtolower(trim($sexe));
        
        if (in_array($sexe, ['m', 'masculin', 'homme', 'garçon', 'male', 'garcon'])) {
            return 'M';
        }
        
        if (in_array($sexe, ['f', 'féminin', 'femme', 'fille', 'female', 'feminin'])) {
            return 'F';
        }
        
        return null;
    }

    private function normaliserTelephone(string $tel): string
    {
        $original = $tel;
        $tel = preg_replace('/[^0-9]/', '', trim($tel));

        if (empty($tel)) {
            return '';
        }

        $len = strlen($tel);
        Log::info("Normalisation téléphone: '{$original}' -> longueur {$len}");

        if ($len === 8) {
            $result = '01' . $tel;
            Log::info("  8 chiffres -> '{$result}'");
            return $result;
        }

        if ($len === 10 && str_starts_with($tel, '01')) {
            Log::info("  10 chiffres avec 01 -> '{$tel}'");
            return $tel;
        }

        if ($len === 11 && str_starts_with($tel, '229')) {
            $result = '22901' . substr($tel, 3);
            Log::info("  11 chiffres 229 -> '{$result}'");
            return $result;
        }

        if ($len === 13 && str_starts_with($tel, '22901')) {
            Log::info("  13 chiffres 22901 -> '{$tel}'");
            return $tel;
        }

        Log::warning("  Téléphone non reconnu: '{$original}' -> conservé tel quel");
        return $tel;
    }

    private function estTelephoneValide(string $tel): bool
    {
        if (empty($tel)) {
            return true;
        }
        $isValid = preg_match('/^(01\d{8}|22901\d{8})$/', $tel) === 1;
        Log::info("Validation téléphone '{$tel}' : " . ($isValid ? 'valide' : 'invalide'));
        return $isValid;
    }

    private function formaterMatricule(string $matricule): string
    {
        $original = $matricule;
        
        if (preg_match('/^(\d{2})-(\d+)$/', $matricule, $matches)) {
            $annee  = $matches[1];
            $numero = $matches[2];

            if (!str_starts_with($numero, '01')) {
                $numero    = '01' . ltrim($numero, '0');
                $matricule = $annee . '-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
                Log::info("Formatage matricule '{$original}' -> '{$matricule}'");
            }
        }

        return strtoupper($matricule);
    }

    private function genererMatriculeUnique(string $base): string
    {
        $original = $base;
        $i        = 1;
        while (Eleve::where('matricule', $base)->exists()) {
            $base = $original . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $i++;
        }
        Log::info("Matricule unique généré: '{$original}' -> '{$base}'");
        return $base;
    }
}