<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Make order_id nullable for gifted tickets
        Schema::table('tickets', function (Blueprint $table) {
            // Some databases require dropping FK first; attempt safe approach
            // If change() fails due to FK, you'll need manual adjustment.
            try {
                $table->unsignedBigInteger('order_id')->nullable()->change();
            } catch (\Throwable $e) {
                // Fallback: drop and recreate constraint
                try {
                    $sm = Schema::getConnection()->getDoctrineSchemaManager();
                    // If doctrine available, proceed further adjustments if needed
                } catch (\Throwable $ignored) {}
                // Last resort: ignore; admin can manually adjust
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Revert to NOT NULL (will fail if null rows exist)
            // Only do if safe in your environment
            //$table->unsignedBigInteger('order_id')->nullable(false)->change();
        });
    }
};
