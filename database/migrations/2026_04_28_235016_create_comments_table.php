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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Relation polymorphique : Book, ShortStory, ou null (blog général)
            $table->nullableMorphs('commentable');

            // Threading : réponse à un commentaire parent (max 2 niveaux)
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();

            // Affichage public
            $table->string('alias', 100)->nullable()->comment("Alias choisi par l'utilisateur");
            $table->text('contenu');

            // Modération automatique
            $table->boolean('is_flagged')->default(false);
            $table->string('flag_reason', 255)->nullable();

            // Statut
            $table->enum('statut', ['publié', 'archivé'])->default('publié');

            // Archivage admin (jamais supprimé physiquement)
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('archive_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
