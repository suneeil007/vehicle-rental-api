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
        Schema::table('users', function (Blueprint $table) {

            $table->string('phone')
                ->nullable()
                ->unique()
                ->after('email');

            $table->unsignedBigInteger('role_id')
                ->nullable()
                ->after('phone');

            $table->unsignedBigInteger('branch_id')
                ->nullable()
                ->after('role_id');

            $table->enum('status', [
                'active',
                'inactive',
                'suspended'
            ])
            ->default('active')
            ->after('password');

        });
    }


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'phone',
                'role_id',
                'branch_id',
                'status'
            ]);

        });
    }
};
