<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('raffles','tickets_total')) {
            Schema::table('raffles', function (Blueprint $table) {
                $table->integer('tickets_total')->default(0)->after('public_stats');
                $table->integer('tickets_sold')->default(0)->after('tickets_total');
            });
        }

        Schema::create('raffle_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price',10,2);
            $table->decimal('amount',10,2);
            $table->string('currency',3);
            $table->string('payment_intent_id')->unique();
            $table->string('status')->default('pending'); // pending|succeeded|failed|expired
            $table->timestamps();
            $table->index(['raffle_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raffle_purchases');
        if (Schema::hasColumn('raffles','tickets_total')) {
            Schema::table('raffles', function (Blueprint $table) {
                $table->dropColumn(['tickets_total','tickets_sold']);
            });
        }
    }
};
