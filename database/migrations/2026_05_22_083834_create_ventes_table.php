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
        if (!Schema::hasTable('ventes')) {
            Schema::create('ventes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
                $table->foreignId('produit_id')->nullable()->constrained('produits')->onDelete('set null');
                $table->foreignId('commercial_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('filiale_id')->constrained('filiales')->onDelete('cascade');
                $table->decimal('montant', 15, 2);
                $table->integer('quantite')->default(1);
                $table->decimal('reduction', 15, 2)->default(0);
                $table->string('statut')->default('En attente')->index(); // En attente, Validée, Annulée
                $table->dateTime('date_vente')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
