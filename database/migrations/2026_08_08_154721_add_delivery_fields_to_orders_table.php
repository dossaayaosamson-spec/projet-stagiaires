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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('type_commande', ['numerique', 'physique'])->default('numerique')->after('statut');
            $table->string('nom_destinataire')->nullable()->after('type_commande');
            $table->string('telephone_livraison')->nullable()->after('nom_destinataire');
            $table->text('adresse_livraison')->nullable()->after('telephone_livraison');
            $table->string('ville_livraison')->nullable()->after('adresse_livraison');
            $table->string('pays_livraison')->nullable()->default('Togo')->after('ville_livraison');
            $table->decimal('frais_livraison', 8, 2)->nullable()->after('pays_livraison');
            $table->string('nom_livreur')->nullable()->after('frais_livraison');
            $table->string('telephone_livreur')->nullable()->after('nom_livreur');
            $table->text('notes_livraison')->nullable()->after('telephone_livreur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'type_commande',
                'nom_destinataire',
                'telephone_livraison',
                'adresse_livraison',
                'ville_livraison',
                'pays_livraison',
                'frais_livraison',
                'nom_livreur',
                'telephone_livreur',
                'notes_livraison',
            ]);
        });
    }
};
