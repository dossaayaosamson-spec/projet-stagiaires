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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('manuscript_id')->nullable()->constrained('manuscripts')->nullOnDelete();
            $table->string('type_contrat', 50)->default('compte_editeur');
            $table->decimal('taux_redevance', 5, 2);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('fichier_contrat_pdf')->nullable();
            $table->string('statut', 50)->default('brouillon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
