<?php

// This migration has been moved to 2026_05_22_083833_create_prospect_histories_table.php
// to fix migration ordering (prospects table must be created before prospect_histories).

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Intentionally empty - see 2026_05_22_083833_create_prospect_histories_table.php
    }

    public function down(): void
    {
        // Intentionally empty - see 2026_05_22_083833_create_prospect_histories_table.php
    }
};
