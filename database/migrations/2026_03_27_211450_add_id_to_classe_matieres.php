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
        Schema::table('classe_matieres', function (Blueprint $table) {
            $table->dropForeign(['classe_annee_id']);
            $table->dropForeign(['matiere_id']);
        });

        Schema::table('classe_matieres', function (Blueprint $table) {
            $table->dropPrimary(['classe_annee_id', 'matiere_id']);
            $table->id()->first();
            $table->unique(['classe_annee_id', 'matiere_id']);
        });

        Schema::table('classe_matieres', function (Blueprint $table) {
            $table->foreign('classe_annee_id')->references('id')->on('classe_annees')->onDelete('cascade');
            $table->foreign('matiere_id')->references('id')->on('matieres')->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::table('classe_matieres', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->primary(['classe_annee_id', 'matiere_id']);
        });
    }
};
