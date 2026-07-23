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
        Schema::create('invoices', function (Blueprint $table) {

            $table->id();

            // Public UUID
            $table->uuid('slug')->unique();

            // Human invoice number
            $table->string('invoice_number')->unique();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            // Invoice is generated from a completed trip
            $table->foreignId('trip_id')
                ->constrained('trips')
                ->cascadeOnDelete();

            // Customer (copied for faster reporting)
            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Invoice Amounts
            |--------------------------------------------------------------------------
            */

            // Base rental amount
            $table->decimal('subtotal', 10, 2)->default(0);

            // Extra charges from trip
            $table->decimal('extra_km_charge', 10, 2)->default(0);
            $table->decimal('late_return_charge', 10, 2)->default(0);
            $table->decimal('damage_charge', 10, 2)->default(0);
            $table->decimal('fuel_charge', 10, 2)->default(0);

            // Discount & tax
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);

            // Final invoice total
            $table->decimal('total_amount', 10, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Payment Summary
            |--------------------------------------------------------------------------
            */

            // Calculated from payments table
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'draft',
                'issued',
                'partially_paid',
                'paid',
                'cancelled',
            ])->default('draft');

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | PDF / Notes
            |--------------------------------------------------------------------------
            */

            // Stored PDF path
            $table->string('pdf_path')->nullable();

            // Internal notes
            $table->text('notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Staff Tracking
            |--------------------------------------------------------------------------
            */

            // Staff who generated the invoice
            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('trip_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('invoice_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};