<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * - Adds 'rejected' to the status enum so admin can reject appraisal requests
     * - admin_note is customer-visible (used to explain reason of rejection / result notes)
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // MySQL requires a raw statement to modify ENUM values
            DB::statement("ALTER TABLE appraisal_requests MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'completed', 'rejected') NOT NULL DEFAULT 'draft'");
        }
        // SQLite stores ENUM as TEXT and does not enforce the constraint,
        // so the new 'rejected' value is automatically accepted without any schema change.
        // For PostgreSQL, use ALTER TYPE or a similar approach if needed.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Revert to original enum — ensure no rows have 'rejected' status before rolling back
            DB::statement("ALTER TABLE appraisal_requests MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'completed') NOT NULL DEFAULT 'draft'");
        }
    }
};
