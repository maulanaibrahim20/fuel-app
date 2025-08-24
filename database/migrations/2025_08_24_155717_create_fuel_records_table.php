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
        Schema::create('fuel_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            // $table->foreignId('gas_station_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('receipt_scan_id')->nullable()->constrained()->nullOnDelete(); // Link ke scan struk
            $table->date('fuel_date');
            $table->decimal('odometer', 12, 2); // Kilometer saat pengisian
            $table->decimal('fuel_amount', 8, 2); // Jumlah liter yang diisi
            $table->decimal('fuel_price_per_liter', 8, 2); // Harga per liter
            $table->decimal('total_cost', 10, 2); // Total biaya
            $table->string('fuel_type');
            $table->boolean('is_full_tank')->default(true); // Apakah full tank
            $table->decimal('trip_distance', 10, 2)->nullable(); // Jarak tempuh sejak pengisian terakhir
            $table->decimal('fuel_consumption', 8, 2)->nullable(); // Konsumsi km/liter
            $table->enum('entry_method', ['manual', 'ocr_scan', 'api_import'])->default('manual');
            $table->text('notes')->nullable();
            $table->string('receipt_image')->nullable(); // Foto struk
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_verified')->default(true); // False jika dari OCR dan perlu verifikasi
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'vehicle_id', 'fuel_date']);
            $table->index(['is_verified', 'entry_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_records');
    }
};
