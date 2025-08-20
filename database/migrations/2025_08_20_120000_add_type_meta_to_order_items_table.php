<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items','type')) {
                $table->string('type',40)->nullable()->after('subtotal');
            }
            if (!Schema::hasColumn('order_items','meta')) {
                $table->json('meta')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items','meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('order_items','type')) {
                $table->dropColumn('type');
            }
        });
    }
};
