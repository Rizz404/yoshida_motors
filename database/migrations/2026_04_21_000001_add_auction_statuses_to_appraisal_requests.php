<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appraisal_requests MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'completed', 'rejected', 'in_auction', 'acquired') NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appraisal_requests MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'completed', 'rejected') NOT NULL DEFAULT 'draft'");
        }
    }
};
