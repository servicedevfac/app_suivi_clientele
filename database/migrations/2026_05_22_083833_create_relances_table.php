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
        Schema::create('relances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->onDelete('cascade');
            $table->foreignId('commercial_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date_relance');
            $table->time('heure_relance')->nullable();
            $table->string('canal')->nullable(); // Appel, WhatsApp, Email, SMS, Rendez-vous
            $table->text('commentaire')->nullable();
            $table->string('statut')->default('En attente')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relances');
    }
};
