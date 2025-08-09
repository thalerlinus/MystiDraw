<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            $table->string('street');
            $table->string('house_number')->nullable();
            $table->string('address2')->nullable();
            $table->string('postal_code');
            $table->string('city');
            $table->string('state')->nullable();
            $table->char('country_code',2);
            $table->string('phone')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['shipping','billing']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            $table->string('street');
            $table->string('house_number')->nullable();
            $table->string('address2')->nullable();
            $table->string('postal_code');
            $table->string('city');
            $table->string('state')->nullable();
            $table->char('country_code',2);
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('order_address_id')->constrained('order_addresses')->cascadeOnDelete();
            $table->enum('status', ['draft','queued','label_printed','shipped','delivered','returned'])->default('draft');
            $table->string('carrier')->nullable();
            $table->string('service')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->integer('weight_g')->nullable();
            $table->decimal('cost',10,2)->nullable();
            $table->char('currency',3)->nullable();
            $table->string('label_path')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignId('user_item_id')->constrained('user_items')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('addresses');
    }
};
