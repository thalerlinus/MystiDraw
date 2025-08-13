<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('country')->nullable()->after('state');
        });
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('country')->nullable()->after('state');
        });
    }

    public function down(): void {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('country');
        });
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
