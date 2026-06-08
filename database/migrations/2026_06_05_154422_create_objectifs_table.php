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
        Schema::create('objectifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_id')->constrained('users')->onDelete('cascade');
            $table->string('mois', 7); // Format YYYY-MM
            $table->decimal('montant_cible', 15, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['commercial_id', 'mois']); // Un seul objectif par mois par commercial
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectifs');
    }
};
