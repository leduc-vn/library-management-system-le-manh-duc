<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Extend the enum to include pending and confirmed and set default to pending
        DB::statement("ALTER TABLE `borrow_slips` MODIFY `status` ENUM('pending','confirmed','borrowing','returned','overdue') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the previous enum values (borrowing, returned, overdue)
        DB::statement("ALTER TABLE `borrow_slips` MODIFY `status` ENUM('borrowing','returned','overdue') NOT NULL DEFAULT 'borrowing'");
    }
};
