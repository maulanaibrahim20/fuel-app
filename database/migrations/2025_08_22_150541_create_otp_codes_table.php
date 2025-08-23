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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // Email atau phone number
            $table->enum('type', ['email', 'phone']); // Jenis OTP
            $table->integer('user_id')->constrained()->onDelete('cascade'); // ID user yang request OTP
            $table->string('code', 6); // Kode OTP (biasanya 6 digit)
            $table->enum('purpose', [
                'phone_verification',
                'email_verification',
                'login',
                'password_reset',
                'account_recovery'
            ]); // Tujuan OTP
            $table->timestamp('expires_at'); // Kapan OTP expired
            $table->boolean('is_used')->default(false); // Sudah digunakan atau belum
            $table->timestamp('used_at')->nullable(); // Kapan digunakan
            $table->string('ip_address')->nullable(); // IP address yang request
            $table->string('user_agent')->nullable(); // User agent
            $table->integer('attempts')->default(0); // Berapa kali dicoba verify
            $table->boolean('is_blocked')->default(false); // Diblokir karena terlalu banyak percobaan
            $table->timestamps();

            // Index untuk performa
            $table->index(['identifier', 'type', 'purpose']);
            $table->index(['code', 'expires_at', 'is_used']);
            $table->index(['expires_at', 'is_used']); // Untuk cleanup expired OTP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
