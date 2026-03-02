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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Boleh null dulu, nanti user update profil

            // EMAIL (Wajib buat Admin, Opsional buat User Mobile)
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();

            // PASSWORD (Wajib buat Admin, Opsional buat User Mobile)
            $table->string('password')->nullable();

            // KHUSUS MOBILE APP (OTP)
            // Format E.164 (+62...) biar standar internasional
            $table->string('phone_number')->unique()->nullable();

            // UID dari Firebase (Penting buat verifikasi keamanan) [cite: 86]
            $table->string('firebase_uid')->unique()->nullable();

            $table->text('address')->nullable();

            // Token FCM buat Push Notif (Kalau user punya banyak device, ini cuma nyimpen yg terakhir)
            $table->string('fcm_token')->nullable();

            // Data Diri Tambahan
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();

            // Auth Provider (Penting untuk mengecek dari mana user login: phone, email, google)
            $table->string('auth_provider')->default('email');

            // Role user
            $table->enum('role', ['user', 'admin'])->default('user');

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
