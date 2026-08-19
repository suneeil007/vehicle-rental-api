<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('llgs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('district_id')
                ->constrained('districts')
                ->cascadeOnDelete();

            $table->string('name', 150);
            $table->string('type', 50);
            $table->string('code', 20)->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('llgs');
    }
};