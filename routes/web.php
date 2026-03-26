<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\TypeNoteController;
use App\Http\Controllers\Admin\PaiementController;
use App\Http\Controllers\Admin\CahierNotesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/index', function () {
    return view('index');
});

// ==================== ROUTES ADMIN ====================
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Parents
    Route::get('parents/import', [Admin\ParentController::class, 'showForm'])->name('parents.import.phpoffice');
    Route::post('parents/import', [Admin\ParentController::class, 'import'])->name('parents.import.phpoffice.post');
    Route::resource('parents', Admin\ParentController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::get('parents/{parent}/reset-password', [Admin\ParentController::class, 'resetPasswordForm'])->name('parents.reset-password.form');
    Route::post('parents/{parent}/reset-password', [Admin\ParentController::class, 'resetPassword'])->name('parents.reset-password');

    // Niveaux, classes, années, matières
    Route::resource('niveaux', Admin\NiveauController::class)->except(['show']);
    Route::resource('classes', Admin\ClasseController::class)->except(['show']);
    Route::resource('annees-scolaires', Admin\AnneeScolaireController::class)->except(['show']);
    Route::resource('classe-annees', Admin\ClasseAnneeController::class)->except(['show']);
    Route::resource('matieres', Admin\MatiereController::class)->except(['show']);

    // Gestion des coefficients pour une classe-année spécifique
    Route::prefix('classe-annees/{classeAnnee}/matieres')->name('classe-matieres.')->group(function () {
        Route::get('/', [Admin\ClasseMatiereController::class, 'index'])->name('index');
        Route::post('/', [Admin\ClasseMatiereController::class, 'store'])->name('store');
        Route::put('{matiere}', [Admin\ClasseMatiereController::class, 'update'])->name('update');
        Route::delete('{matiere}', [Admin\ClasseMatiereController::class, 'destroy'])->name('destroy');
    });

    // Scolarités
    Route::resource('scolarites', App\Http\Controllers\Admin\ScolariteController::class)->except(['show']);

    // Tranches imbriquées dans une scolarité
    Route::prefix('scolarites/{scolarite}/tranches')->name('tranches.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TrancheController::class, 'index'])->name('index');
        Route::get('create', [App\Http\Controllers\Admin\TrancheController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\TrancheController::class, 'store'])->name('store');
        Route::get('{tranche}/edit', [App\Http\Controllers\Admin\TrancheController::class, 'edit'])->name('edit');
        Route::put('{tranche}', [App\Http\Controllers\Admin\TrancheController::class, 'update'])->name('update');
        Route::delete('{tranche}', [App\Http\Controllers\Admin\TrancheController::class, 'destroy'])->name('destroy');
    });

    // Élèves
    Route::resource('eleves', Admin\EleveController::class)->only(['index']);
    
    // Routes d'import des élèves
    Route::prefix('eleves/import')->name('eleves.import.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ImportEleveController::class, 'create'])->name('create');
        Route::get('/preview', [App\Http\Controllers\Admin\ImportEleveController::class, 'preview'])->name('preview');
        Route::post('/process', [App\Http\Controllers\Admin\ImportEleveController::class, 'process'])->name('process'); // ← AJOUTEZ CETTE LIGNE
        Route::post('/store', [App\Http\Controllers\Admin\ImportEleveController::class, 'store'])->name('store');
    });
    
    // Paiements
    Route::get('paiements',           [Admin\PaiementController::class, 'index'])->name('paiements.index');
    Route::get('paiements/debiteurs', [Admin\PaiementController::class, 'debiteurs'])->name('paiements.debiteurs');
    Route::get('paiements/create',    [Admin\PaiementController::class, 'create'])->name('paiements.create');
    Route::post('paiements',          [Admin\PaiementController::class, 'store'])->name('paiements.store');
    
    // Notes
    Route::get('/preview', [Admin\NoteController::class, 'preview'])->name('preview.get');
    Route::resource('type-notes', TypeNoteController::class)->except(['show']);

    // Périodes
    Route::resource('periodes', App\Http\Controllers\Admin\PeriodeController::class)->except(['show']);

    Route::get('/notes',                   [NoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/create',            [NoteController::class, 'create'])->name('notes.create');
    Route::match(['get','post'],'/notes/preview', [NoteController::class, 'preview'])->name('notes.preview');
    Route::get('/notes/export-template',   [NoteController::class, 'exportTemplate'])->name('notes.export-template');
    Route::post('/notes/import-preview',   [NoteController::class, 'importPreview'])->name('notes.import-preview');
    Route::post('/notes/import-image',     [NoteController::class, 'importImage'])->name('notes.import-image');
    Route::get('/notes/export-pdf',        [NoteController::class, 'exportPdf'])->name('notes.export-pdf');
    Route::post('/notes',                  [NoteController::class, 'store'])->name('notes.store');

    // Cahier de notes
    Route::prefix('cahier-notes')->name('cahier-notes.')->group(function () {
        Route::get('/', [CahierNotesController::class, 'index'])->name('index');
        Route::get('/classe/{classeAnnee}', [CahierNotesController::class, 'classe'])->name('classe');
        Route::get('/classe/{classeAnnee}/eleves', [CahierNotesController::class, 'listeEleves'])->name('liste-eleves');
        Route::get('/eleve/{eleve}', [CahierNotesController::class, 'bulletinEleve'])->name('bulletin-eleve');
    });

    // Communiqués
    Route::resource('communiques', \App\Http\Controllers\Admin\CommuniqueController::class);
    Route::patch('communiques/{communique}/toggle', [\App\Http\Controllers\Admin\CommuniqueController::class, 'toggle'])->name('communiques.toggle');
});

// Routes supplémentaires
Route::get('notes/export-template-excel', [NoteController::class, 'exportTemplateExcel'])
     ->name('admin.notes.export-template-excel');

// ==================== ROUTES ENSEIGNANT ====================
Route::prefix('enseignant')->name('enseignant.')->middleware(['auth', 'role:enseignant'])->group(function () {
    Route::get('/dashboard', function () {
        return view('enseignant.dashboard');
    })->name('dashboard');
});

// ==================== ROUTES PARENT ====================
Route::prefix('parent')->name('parent.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboardparent');
    })->name('dashboard');
});

// ==================== ROUTES PAR DÉFAUT ====================
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ==================== PROFIL ====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==================== AUTH ====================
require __DIR__ . '/auth.php';