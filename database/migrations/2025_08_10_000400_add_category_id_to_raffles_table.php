<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('raffles', function (Blueprint $table) {
            $table->foreignId('category_id')->after('id')->constrained('categories')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
