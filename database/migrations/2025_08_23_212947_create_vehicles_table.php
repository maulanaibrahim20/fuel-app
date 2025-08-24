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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Nama kendaraan yang diberi user
            $table->string('brand'); // Merk kendaraan
            $table->string('model'); // Model kendaraan
            $table->year('year'); // Tahun kendaraan
            $table->enum('fuel_type', ['gasoline', 'diesel', 'hybrid', 'electric', 'lpg', 'pertamax', 'pertamax_plus']);
            $table->enum('transmission', ['manual', 'automatic', 'cvt']);
            $table->decimal('engine_capacity', 4, 2)->nullable(); // CC mesin
            $table->string('license_plate');
            $table->decimal('tank_capacity', 8, 2)->nullable(); // Kapasitas tangki
            $table->string('color')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable(); // Foto kendaraan
            $table->decimal('initial_odometer', 12, 2)->default(0); // KM awal saat input
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
