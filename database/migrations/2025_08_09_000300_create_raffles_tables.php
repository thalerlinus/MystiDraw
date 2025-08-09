<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('status', ['draft','scheduled','live','paused','sold_out','finished','archived'])->default('draft');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->decimal('base_ticket_price', 10, 2);
            $table->char('currency',3);
            $table->boolean('public_stats')->default(false);
            $table->timestamps();
        });

        Schema::create('raffle_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->cascadeOnDelete();
            $table->integer('min_qty');
            $table->decimal('unit_price', 10,2);
            $table->timestamps();
        });

        Schema::create('raffle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('tier', ['A','B','C','D','E']);
            $table->integer('quantity_total');
            $table->integer('quantity_awarded')->default(0);
            $table->integer('weight')->default(1);
            $table->boolean('is_last_one')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('raffle_items');
        Schema::dropIfExists('raffle_pricing_tiers');
        Schema::dropIfExists('raffles');
    }
};
