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

            // Self-drive or with-driver
            $table->enum('rental_type', [
                'self_drive',
                'with_driver'
            ])->default('self_drive')
              ->after('vehicle_id');

            // Assigned company driver
            $table->foreignId('driver_id')
                ->nullable()
                ->after('rental_type')
                ->constrained('users')
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {

            $table->dropConstrainedForeignId('driver_id');

            $table->dropColumn('rental_type');

        });
    }
};