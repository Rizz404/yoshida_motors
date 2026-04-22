<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_request_id')->constrained('appraisal_requests')->onDelete('cascade');
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('overall_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_reports');
    }
};
