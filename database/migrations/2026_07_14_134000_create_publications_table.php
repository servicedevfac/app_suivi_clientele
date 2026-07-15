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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campagne_id')->constrained('campagnes')->onDelete('cascade');
            $table->string('titre');
            $table->string('canal')->index(); // e.g. LinkedIn, Facebook, Instagram, Google Ads, Emailing, Affichage, Salon, Autre
            $table->string('url_support')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->date('date_publication')->nullable();
            $table->string('statut')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
