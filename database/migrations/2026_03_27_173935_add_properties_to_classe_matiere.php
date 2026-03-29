<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ajouter enseignant_id à classe_matieres
        Schema::table('classe_matieres', function (Blueprint $table) {
            $table->foreignId('enseignant_id')
                  ->nullable()
                  ->after('coefficient')
                  ->constrained('enseignants')
                  ->onDelete('set null');
        });

        // 2. Table des séances (emploi du temps)
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_annee_id')->constrained('classe_annees')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->foreignId('enseignant_id')->constrained('enseignants')->onDelete('cascade');
            // 0=Lundi, 1=Mardi, 2=Mercredi, 3=Jeudi, 4=Vendredi, 5=Samedi
            $table->unsignedTinyInteger('jour_semaine');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->timestamps();

            // Un enseignant ne peut pas avoir deux séances qui se chevauchent le même jour
            // Cette contrainte est gérée en PHP (trop complexe en SQL pur)
            // Mais on évite les doublons exacts
            $table->unique(['enseignant_id', 'jour_semaine', 'heure_debut'], 'unique_enseignant_slot');
            $table->unique(['classe_annee_id', 'matiere_id', 'jour_semaine', 'heure_debut'], 'unique_classe_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seances');

        Schema::table('classe_matieres', function (Blueprint $table) {
            $table->dropForeign(['enseignant_id']);
            $table->dropColumn('enseignant_id');
        });
    }
};
