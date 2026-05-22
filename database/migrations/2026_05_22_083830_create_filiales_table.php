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
        Schema::create('filiales', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('adresse')->nullable();
            $table->string('telephone')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();
            $table->string('statut')->default('actif')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filiales');
    }
};
