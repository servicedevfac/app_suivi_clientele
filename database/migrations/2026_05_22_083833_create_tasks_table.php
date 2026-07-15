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
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('prospect_id')->nullable()->constrained('prospects')->onDelete('set null');
                $table->string('titre');
                $table->text('description')->nullable();
                $table->string('priorite')->default('Moyenne')->index(); // Faible, Moyenne, Haute, Urgente
                $table->dateTime('date_limite')->nullable();
                $table->string('statut')->default('À faire')->index();
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
        Schema::dropIfExists('tasks');
    }
};
