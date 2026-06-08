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
        Schema::table('prospects', function (Blueprint $table) {
            $table->decimal('montant_estime', 15, 2)->nullable()->after('besoin');
            $table->integer('probabilite')->default(0)->after('montant_estime'); // Pourcentage de chance de conclure
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropColumn(['montant_estime', 'probabilite']);
        });
    }
};
