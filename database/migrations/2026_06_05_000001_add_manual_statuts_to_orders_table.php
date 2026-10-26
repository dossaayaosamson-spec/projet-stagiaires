<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN statut ENUM('en_attente', 'confirmée', 'livrée', 'annulée', 'paye', 'en_attente_transfert', 'en_attente_verification') DEFAULT 'en_attente'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN statut ENUM('en_attente', 'confirmée', 'livrée', 'annulée', 'paye') DEFAULT 'en_attente'");
    }
};
