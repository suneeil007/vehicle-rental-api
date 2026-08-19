<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('pickup_branch_id')
                ->nullable()
                ->change();

            $table->unsignedBigInteger('drop_branch_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('pickup_branch_id')
                ->nullable(false)
                ->change();

            $table->unsignedBigInteger('drop_branch_id')
                ->nullable()
                ->change();
        });
    }
};