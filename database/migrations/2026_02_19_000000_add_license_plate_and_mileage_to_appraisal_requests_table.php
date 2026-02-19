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
    Schema::table('appraisal_requests', function (Blueprint $table) {
      $table->string('license_plate')->nullable()->after('description');
      $table->unsignedInteger('mileage')->nullable()->after('license_plate'); // in km
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('appraisal_requests', function (Blueprint $table) {
      $table->dropColumn(['license_plate', 'mileage']);
    });
  }
};
