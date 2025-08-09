<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending','paid','failed','refunded','cancelled'])->default('pending');
            $table->decimal('total', 10,2);
            $table->char('currency',3);
            $table->decimal('provider_fee', 10,2)->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->json('meta')->nullable();
            // Added later after addresses table exists to avoid circular migration dependency
            $table->foreignId('shipping_address_id')->nullable()->index();
            $table->foreignId('billing_address_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raffle_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price',10,2);
            $table->decimal('subtotal',10,2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('provider', ['stripe','paypal','manual','other']);
            $table->string('provider_txn_id')->nullable();
            $table->decimal('amount',10,2);
            $table->char('currency',3);
            $table->enum('status', ['pending','succeeded','failed','refunded'])->default('pending');
            $table->dateTime('paid_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
