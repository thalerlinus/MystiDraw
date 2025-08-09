<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('serial')->unique();
            $table->decimal('price_paid',10,2);
            $table->enum('status', ['created','paid','drawn','refunded','void'])->default('created');
            $table->dateTime('drawn_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('raffle_item_id')->nullable()->constrained('raffle_items')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->enum('tier', ['A','B','C','D','E']);
            $table->enum('decided_by', ['instant','batch']);
            $table->string('rng_seed')->nullable();
            $table->decimal('rng_roll', 10,6)->nullable();
            $table->enum('status', ['assigned','fulfilled'])->default('assigned');
            $table->dateTime('assigned_at');
            $table->dateTime('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('ticket_outcomes');
        Schema::dropIfExists('tickets');
    }
};
