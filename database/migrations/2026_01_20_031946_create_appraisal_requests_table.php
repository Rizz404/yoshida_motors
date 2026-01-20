<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appraisal_requests', function (Blueprint $table) {
            $table->id();
            // Relasi ke User
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Data Kendaraan (Input Manual User)
            $table->string('vehicle_brand'); // e.g. "Honda"
            $table->string('vehicle_model'); // e.g. "Jazz"
            $table->integer('year_manufacture');
            $table->text('description')->nullable();

            // Status Workflow
            // Draft = belum dikirim, Submitted = dikirim ke admin, Completed = selesai
            $table->enum('status', ['draft', 'submitted', 'under_review', 'completed'])->default('draft');

            // Hasil Appraisal (Diisi Admin)
            $table->decimal('final_price', 15, 2)->nullable();
            $table->text('admin_note')->nullable();
            $table->dateTime('price_valid_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisal_requests');
    }
};
