<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_outcome_id')->unique()->constrained('ticket_outcomes')->cascadeOnDelete();
            $table->enum('status', ['owned','reserved_for_shipping','shipped'])->default('owned');
            $table->dateTime('owned_at');
            $table->dateTime('shipped_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->foreignId('last_ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id','product_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_inventory');
        Schema::dropIfExists('user_items');
    }
};
