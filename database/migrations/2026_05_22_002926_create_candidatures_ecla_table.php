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
        Schema::create('candidatures_ecla', function (Blueprint $table) {
            $table->id();
            // Section 1: Informations Personnelles
            $table->string('nom');
            $table->string('prenoms');
            $table->string('genre'); // Masculin / Féminin
            $table->integer('age');
            $table->string('nationalite');
            $table->string('pays_residence');
            $table->string('ville_residence');
            $table->string('quartier');
            $table->string('whatsapp');
            $table->string('email');
            $table->string('profession');

            // Section 2: Informations sur l'œuvre
            $table->string('titre_manuscrit');
            $table->string('genre_litteraire'); // Roman / Nouvelle
            $table->text('resume'); // Entre 150 et 300 mots
            $table->integer('nombre_mots');
            $table->boolean('deja_publie');
            $table->boolean('soumis_ailleurs');

            // Section 3: Téléchargement
            $table->string('fichier_manuscrit'); // Path to stored docx file

            // Section 4: Engagement du candidat
            $table->boolean('declaration_originalite'); // True if checked all original declarations
            $table->boolean('acceptation_reglement'); // True if checked official rules
            $table->boolean('compte_agau'); // True if they have a website account
            $table->string('source_decouverte'); // WhatsApp, Facebook, Instagram, TikTok, Université, Média/Presse, Site web, Autre
            $table->string('source_decouverte_autre')->nullable();

            // Admin fields
            $table->string('statut')->default('en_attente'); // en_attente, en_cours, acceptee, refusee
            $table->text('notes_admin')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures_ecla');
    }
};
