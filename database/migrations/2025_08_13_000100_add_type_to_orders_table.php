<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('type', 30)->nullable()->index()->after('status');
        });

        // Backfill existing records heuristically
        // shipping: has shipping_cost in meta
        DB::statement("UPDATE orders SET type='shipping' WHERE type IS NULL AND JSON_EXTRACT(meta,'$.shipping_cost') IS NOT NULL");
        // raffle: has raffle_id in meta
        DB::statement("UPDATE orders SET type='raffle' WHERE type IS NULL AND JSON_EXTRACT(meta,'$.raffle_id') IS NOT NULL");
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
