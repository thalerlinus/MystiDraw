<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('credit_note_number')->nullable()->unique()->after('invoice_number');
            $table->timestamp('refund_email_sent_at')->nullable()->after('email_sent_at');
        });
        // Optional: Erweiterung invoice_counters Tabelle
        if (Schema::hasTable('invoice_counters') && !Schema::hasColumn('invoice_counters','last_credit_sequence')) {
            Schema::table('invoice_counters', function (Blueprint $table) {
                $table->unsignedInteger('last_credit_sequence')->default(0);
            });
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['credit_note_number']);
            $table->dropColumn(['credit_note_number','refund_email_sent_at']);
        });
        if (Schema::hasTable('invoice_counters') && Schema::hasColumn('invoice_counters','last_credit_sequence')) {
            Schema::table('invoice_counters', function (Blueprint $table) {
                $table->dropColumn('last_credit_sequence');
            });
        }
    }
};
