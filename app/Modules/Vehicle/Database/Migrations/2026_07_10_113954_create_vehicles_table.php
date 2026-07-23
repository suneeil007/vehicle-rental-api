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

                $table->foreignId('vehicle_category_id')
                    ->constrained('vehicle_categories')
                    ->cascadeOnDelete();

                $table->string('name');
                $table->string('slug')->unique();

                $table->string('brand');
                $table->string('model');
                $table->year('manufacture_year')->nullable();

                $table->enum('transmission', [
                    'manual',
                    'automatic'
                ]);

                $table->enum('fuel_type', [
                    'petrol',
                    'diesel',
                    'electric',
                    'hybrid'
                ]);

                $table->integer('seat_capacity');

                $table->decimal('price_per_day', 10, 2);

                $table->string('registration_number')->unique();

                $table->integer('mileage')->nullable();

                $table->string('color')->nullable();

                $table->text('description')->nullable();

                $table->enum('status', [
                    'available',
                    'booked',
                    'maintenance',
                    'inactive'
                ])->default('available');

                $table->timestamps();
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
