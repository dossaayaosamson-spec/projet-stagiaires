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
        Schema::create('admin_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();

            // Action effectuée (ex: 'archive_comment', 'resolve_alias', 'suspend_user', 'lift_suspension')
            $table->string('action', 100);

            // Cible polymorphique (Comment, User, etc.)
            $table->nullableMorphs('target');

            // Détails additionnels (motif, alias résolu, etc.)
            $table->json('details')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_action_logs');
    }
};
