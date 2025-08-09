<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('raffle_category', function (Blueprint $table) {
            $table->foreignId('raffle_id')->constrained('raffles')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['raffle_id','category_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('raffle_category');
    }
};
