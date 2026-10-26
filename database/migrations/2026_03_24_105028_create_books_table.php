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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade');
            $table->string('categorie', 100)->nullable();
            $table->text('resume')->nullable();
            $table->string('badge', 50)->nullable(); // ex: "Nouveau", "Bestseller"
            $table->string('couleur_couverture', 50)->default('#1B6CA8');
            $table->decimal('prix', 8, 2)->default(0);
            $table->boolean('is_bestseller')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
