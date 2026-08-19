<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Pickup Branch
            |--------------------------------------------------------------------------
            |
            | With Driver:
            |     Required.
            |
            | Self Drive:
            |     Can be NULL because pickup_location is used.
            |
            */

            $table->foreignId('pickup_branch_id')
                ->nullable()
                ->change();

            /*
            |--------------------------------------------------------------------------
            | Pickup / Drop Location
            |--------------------------------------------------------------------------
            */

            $table->string('pickup_location', 500)
                ->nullable()
                ->after('pickup_branch_id');

            $table->string('drop_location', 500)
                ->nullable()
                ->after('drop_branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {

            $table->dropColumn([
                'pickup_location',
                'drop_location',
            ]);

            $table->foreignId('pickup_branch_id')
                ->nullable(false)
                ->change();
        });
    }
};