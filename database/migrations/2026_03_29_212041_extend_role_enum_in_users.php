<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum directement en SQL — Blueprint ne supporte pas
        // la modification d'un enum existant de façon portable
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM(
                'admin',
                'enseignant',
                'parent',
                'censeur',
                'secretaire',
                'comptable',
                'surveillant'
            ) NOT NULL DEFAULT 'parent'
        ");
    }

    public function down(): void
    {
        // Revenir à l'enum d'origine
        // Attention : les users avec les nouveaux rôles seront en erreur
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM(
                'admin',
                'enseignant',
                'parent'
            ) NOT NULL DEFAULT 'parent'
        ");
    }
};
