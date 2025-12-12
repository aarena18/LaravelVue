<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Duplicate/obsolete migration kept as a no-op to avoid creating the same table twice.
// The real migration creating the `api_keys` table is `2025_12_12_132730_api_keys.php`.

return new class extends Migration {
    public function up(): void
    {
        // intentionally left empty
    }

    public function down(): void
    {
        // intentionally left empty
    }
};
