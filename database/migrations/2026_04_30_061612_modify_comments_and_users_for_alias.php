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
        // 1. Ajouter la colonne alias à la table users
        Schema::table('users', function (Blueprint $table) {
            $table->string('alias')->nullable()->unique()->after('nom_famille');
        });

        // 2. Migrer les données existantes
        $usersWithAlias = DB::table('comments')
            ->whereNotNull('alias')
            ->where('alias', '!=', '')
            ->select('user_id', 'alias', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('user_id');

        foreach ($usersWithAlias as $entry) {
            try {
                DB::table('users')
                    ->where('id', $entry->user_id)
                    ->update(['alias' => $entry->alias]);
            } catch (Exception $e) {
                // En cas de conflit d'unicité, on ignore
            }
        }

        // 3. Supprimer la colonne alias de la table comments
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('alias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Recréer la colonne alias sur comments
        Schema::table('comments', function (Blueprint $table) {
            $table->string('alias')->nullable()->after('parent_id');
        });

        // 2. Restaurer les données
        DB::table('users')
            ->whereNotNull('alias')
            ->orderBy('id')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    DB::table('comments')
                        ->where('user_id', $user->id)
                        ->update(['alias' => $user->alias]);
                }
            });

        // 3. Supprimer la colonne alias de users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('alias');
        });
    }
};
