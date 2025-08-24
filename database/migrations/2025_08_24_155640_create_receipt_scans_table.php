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
        Schema::create('receipt_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('original_image_path'); // Path foto asli
            $table->string('processed_image_path')->nullable(); // Path foto yang sudah diproses
            $table->json('ocr_raw_data'); // Raw data dari OCR
            $table->json('extracted_data')->nullable(); // Data yang berhasil diekstrak
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->enum('scan_type', ['fuel_receipt', 'maintenance_receipt', 'other'])->default('fuel_receipt');
            $table->text('error_message')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable(); // Tingkat kepercayaan OCR (0-100)
            $table->boolean('is_manually_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'scan_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_scans');
    }
};
