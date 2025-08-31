<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_recoveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('raffle_item_id')->nullable()->constrained('raffle_items')->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamp('purged_at');
            $table->timestamps();
            $table->index('product_id');
            $table->index('raffle_item_id');
            $table->index('purged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_recoveries');
    }
};
