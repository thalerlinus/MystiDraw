<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('raffles','next_ticket_serial')) {
            Schema::table('raffles', function (Blueprint $table) {
                $table->unsignedBigInteger('next_ticket_serial')->default(0)->after('tickets_sold');
            });
        }
    }
    public function down(): void {
        if (Schema::hasColumn('raffles','next_ticket_serial')) {
            Schema::table('raffles', function (Blueprint $table) {
                $table->dropColumn('next_ticket_serial');
            });
        }
    }
};
