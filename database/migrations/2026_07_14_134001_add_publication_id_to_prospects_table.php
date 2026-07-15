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
            $table->foreignId('publication_id')->nullable()->after('campagne_id')->constrained('publications')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropForeign(['publication_id']);
            $table->dropColumn('publication_id');
        });
    }
};
