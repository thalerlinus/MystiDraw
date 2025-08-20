<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // FK lösen, Spalte nullable machen, FK neu setzen
        Schema::table('payments', function (Blueprint $table) {
            try { $table->dropForeign(['order_id']); } catch (\Throwable $e) {}
        });
        Schema::table('payments', function (Blueprint $table) {
            // Benötigt doctrine/dbal für change(); falls nicht installiert -> composer require doctrine/dbal
            $table->unsignedBigInteger('order_id')->nullable()->change();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Rückgängig (nur wenn keine NULLs mehr vorhanden)
        Schema::table('payments', function (Blueprint $table) {
            try { $table->dropForeign(['order_id']); } catch (\Throwable $e) {}
        });
        // Achtung: schlägt fehl wenn NULL Werte existieren
        Schema::table('payments', function (Blueprint $table) {
            try { $table->unsignedBigInteger('order_id')->nullable(false)->change(); } catch (\Throwable $e) {}
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }
};
