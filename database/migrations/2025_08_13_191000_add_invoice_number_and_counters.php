<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->unique()->after('provider_txn_id');
        });

        Schema::create('invoice_counters', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->unsignedInteger('last_sequence')->default(0);
            $table->timestamps();
            $table->unique('year');
        });
    }

    public function down(): void {
        Schema::dropIfExists('invoice_counters');
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']);
            $table->dropColumn('invoice_number');
        });
    }
};
