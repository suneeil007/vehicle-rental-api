<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {

            $table->id();

            $table->uuid('slug')->unique();

            // Customer
            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Vehicle
            $table->foreignId('vehicle_id')
                ->constrained()
                ->cascadeOnDelete();

            // Staff who handed over vehicle
            $table->foreignId('staff_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Pickup / Drop branches
            $table->foreignId('pickup_branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            $table->foreignId('drop_branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Schedule
            $table->dateTime('pickup_at');

            $table->dateTime('expected_return_at');

            $table->dateTime('actual_return_at')->nullable();

            // Odometer
            $table->unsignedBigInteger('pickup_odometer');

            $table->unsignedBigInteger('return_odometer')->nullable();

            // Fuel
            $table->enum('pickup_fuel', [
                'empty',
                'quarter',
                'half',
                'three_quarter',
                'full'
            ]);

            $table->enum('return_fuel', [
                'empty',
                'quarter',
                'half',
                'three_quarter',
                'full'
            ])->nullable();

            // Charges
            $table->decimal('base_amount', 10, 2)->default(0);

            $table->decimal('extra_km_charge', 10, 2)->default(0);

            $table->decimal('late_return_charge', 10, 2)->default(0);

            $table->decimal('damage_charge', 10, 2)->default(0);

            $table->decimal('fuel_charge', 10, 2)->default(0);

            $table->decimal('total_amount', 10, 2)->default(0);

            // Trip status
            $table->enum('status', [
                'scheduled',
                'picked_up',
                'on_trip',
                'completed',
                'cancelled'
            ])->default('scheduled');

            // Notes
            $table->text('pickup_notes')->nullable();

            $table->text('return_notes')->nullable();

            $table->text('damage_notes')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};