<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SQLite does enforce the CHECK constraint created by enum(), so the
     * 'rejected', 'in_auction' and 'acquired' statuses added for MySQL in
     * earlier migrations were rejected on SQLite. Rebuild the column there.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        Schema::table('appraisal_requests', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'under_review', 'completed', 'rejected', 'in_auction', 'acquired'])
                ->default('draft')
                ->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        Schema::table('appraisal_requests', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'under_review', 'completed'])
                ->default('draft')
                ->change();
        });
    }
};
