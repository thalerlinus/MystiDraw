<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'thumbnail_path')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('thumbnail_path')->nullable()->after('default_tier');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'thumbnail_path')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('thumbnail_path');
            });
        }
    }
};
