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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('source_id')->nullable()->constrained('sources')->onDelete('set null');
            $table->foreignId('campagne_id')->nullable()->constrained('campagnes')->onDelete('set null');
            $table->foreignId('filiale_id')->constrained('filiales')->onDelete('cascade');
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('telephone')->nullable()->index();
            $table->string('entreprise')->nullable();
            $table->string('profession')->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('statut')->default('Nouveau')->index(); // Nouveau, Contacté, Qualifié, En négociation, Gagné, Perdu
            $table->text('besoin')->nullable();
            $table->text('commentaire')->nullable();
            $table->dateTime('date_contact')->nullable();
            $table->dateTime('prochain_rappel')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
