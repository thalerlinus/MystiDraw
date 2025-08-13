<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Einfach: direkt neue ENUM Definition setzen (MySQL)
        DB::statement("ALTER TABLE ticket_outcomes MODIFY status ENUM('assigned','reserved_for_shipping','fulfilled') NOT NULL DEFAULT 'assigned'");
    }

    public function down(): void {
        // Zurück auf ursprüngliche Definition
        DB::statement("ALTER TABLE ticket_outcomes MODIFY status ENUM('assigned','fulfilled') NOT NULL DEFAULT 'assigned'");
    }
};
