<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('provinces')->insert([
            [
                'name' => 'Koshi Province',
                'code' => 'KOSHI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Madhesh Province',
                'code' => 'MADHESH',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bagmati Province',
                'code' => 'BAGMATI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gandaki Province',
                'code' => 'GANDAKI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lumbini Province',
                'code' => 'LUMBINI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Karnali Province',
                'code' => 'KARNALI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sudurpashchim Province',
                'code' => 'SUDURPASHCHIM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}