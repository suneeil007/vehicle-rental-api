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
        Schema::table('trips', function (Blueprint $table) {

            // Who cancelled the trip
            $table->foreignId('cancelled_by')
                ->nullable()
                ->after('return_staff_id')
                ->constrained('users')
                ->nullOnDelete();

            // Why it was cancelled
            $table->text('cancellation_reason')
                ->nullable()
                ->after('damage_notes');

            // When it was cancelled
            $table->timestamp('cancelled_at')
                ->nullable()
                ->after('cancellation_reason');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {

            $table->dropConstrainedForeignId('cancelled_by');

            $table->dropColumn([
                'cancellation_reason',
                'cancelled_at',
            ]);

        });
    }
};