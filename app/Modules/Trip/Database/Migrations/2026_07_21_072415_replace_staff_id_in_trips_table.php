<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {

            // Add new staff fields
            $table->foreignId('pickup_staff_id')
                ->nullable()
                ->after('vehicle_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('return_staff_id')
                ->nullable()
                ->after('pickup_staff_id')
                ->constrained('users')
                ->nullOnDelete();

            // Remove old staff_id
            $table->dropConstrainedForeignId('staff_id');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {

            // Restore old staff_id
            $table->foreignId('staff_id')
                ->nullable()
                ->after('vehicle_id')
                ->constrained('users')
                ->nullOnDelete();

            // Remove new fields
            $table->dropConstrainedForeignId('pickup_staff_id');
            $table->dropConstrainedForeignId('return_staff_id');
        });
    }
};