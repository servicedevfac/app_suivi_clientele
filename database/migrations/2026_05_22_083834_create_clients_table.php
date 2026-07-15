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
        if (!Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prospect_id')->nullable()->constrained('prospects')->onDelete('set null');
                $table->foreignId('commercial_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('filiale_id')->constrained('filiales')->onDelete('cascade');
                $table->string('nom');
                $table->string('prenom')->nullable();
                $table->string('email')->nullable()->index();
                $table->string('telephone')->nullable()->index();
                $table->text('adresse')->nullable();
                $table->string('ville')->nullable();
                $table->string('entreprise')->nullable();
                $table->string('statut')->default('Actif')->index();
                $table->dateTime('date_conversion')->nullable();
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
        Schema::dropIfExists('clients');
    }
};
