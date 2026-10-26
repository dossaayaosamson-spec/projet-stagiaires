<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite ne supporte pas MODIFY COLUMN + ENUM en SQL brut.
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN statut ENUM('en_attente', 'confirmée', 'livrée', 'annulée', 'paye') DEFAULT 'en_attente'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN statut ENUM('en_attente', 'confirmée', 'livrée', 'annulée') DEFAULT 'en_attente'");
    }
};
