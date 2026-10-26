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
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->string('submission_source', 50)->default('lecthor_public')->after('user_id');
            $table->string('pen_name')->nullable()->after('author_name');
            $table->string('portrait_path')->nullable()->after('file_path');
            $table->string('city')->nullable()->after('portrait_path');
            $table->string('country')->nullable()->after('city');
            $table->text('social_links')->nullable()->after('country');
            $table->text('bio')->nullable()->after('synopsis');
            $table->text('intent_note')->nullable()->after('bio');
            $table->boolean('certified_original')->default(false)->after('intent_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->dropColumn([
                'submission_source',
                'pen_name',
                'portrait_path',
                'city',
                'country',
                'social_links',
                'bio',
                'intent_note',
                'certified_original',
            ]);
        });
    }
};
