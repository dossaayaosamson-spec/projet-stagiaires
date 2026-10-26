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
        Schema::create('short_stories', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('extrait');
            $table->string('auteur', 150);
            $table->json('tags')->nullable(); // ex: ["Romance", "Suspense"]
            $table->string('gradient', 100)->nullable(); // ex: "from-blue-800 to-indigo-900"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_stories');
    }
};
