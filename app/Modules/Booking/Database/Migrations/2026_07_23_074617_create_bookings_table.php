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
        Schema::create('bookings', function (Blueprint $table) {
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

            // Rental
            $table->enum('rental_type', ['self_drive', 'with_driver'])
                ->default('self_drive');

            // Branches
            $table->foreignId('pickup_branch_id')
                ->constrained('branches');

            $table->foreignId('drop_branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Schedule
            $table->dateTime('pickup_at');
            $table->dateTime('expected_return_at');

            // Pricing
            $table->decimal('quoted_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);

            // Approval
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            // Link to generated trip
            $table->foreignId('trip_id')
                ->nullable()
                ->constrained('trips')
                ->nullOnDelete();

            // Status
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled',
                'trip_created',
                'completed'
            ])->default('pending');

            // Notes
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
