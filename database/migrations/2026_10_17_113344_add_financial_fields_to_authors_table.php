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
        Schema::table('authors', function (Blueprint $table) {
            $table->decimal('solde_disponible', 12, 2)->default(0)->after('nb_livres');
            $table->decimal('total_gagne', 12, 2)->default(0)->after('solde_disponible');
            $table->decimal('total_retire', 12, 2)->default(0)->after('total_gagne');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn(['solde_disponible', 'total_gagne', 'total_retire']);
        });
    }
};
