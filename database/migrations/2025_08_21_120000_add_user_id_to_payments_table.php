<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments','user_id')) {
                $table->foreignId('user_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            }
        });
        // Backfill für bestehende Datensätze anhand Order
        try {
            DB::statement('UPDATE payments p JOIN orders o ON p.order_id = o.id SET p.user_id = o.user_id WHERE p.order_id IS NOT NULL AND p.user_id IS NULL');
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments','user_id')) {
                try { $table->dropConstrainedForeignId('user_id'); } catch (\Throwable $e) {}
            }
        });
    }
};
