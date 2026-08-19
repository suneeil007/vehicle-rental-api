<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LlgSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/local_levels.json');

        if (!File::exists($file)) {
            $this->command->error(
                'local_levels.json not found at: ' . $file
            );

            return;
        }

        $data = json_decode(
            File::get($file),
            true
        );

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error(
                'Invalid JSON: ' . json_last_error_msg()
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Get districts
        |--------------------------------------------------------------------------
        |
        | Your DistrictSeeder uses district codes such as:
        | KATHMANDU, KASKI, MORANG, etc.
        |
        */

        $districts = DB::table('districts')
            ->pluck('id', 'code');

        $llgs = [];

        foreach ($data as $item) {

            /*
            |--------------------------------------------------------------------------
            | Adjust these keys according to the JSON structure
            |--------------------------------------------------------------------------
            */

            $districtName = strtoupper(
                trim($item['district'] ?? '')
            );

            $name = trim(
                $item['name'] ?? $item['name_en'] ?? ''
            );

            $type = strtolower(
                trim($item['type'] ?? '')
            );

            if (!$districtName || !$name) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Convert district name to your district code
            |--------------------------------------------------------------------------
            */

            $districtCode = $this->districtCode($districtName);

            if (!isset($districts[$districtCode])) {
                $this->command->warn(
                    "District not found: {$districtName}"
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Normalize LLG type
            |--------------------------------------------------------------------------
            */

            $type = match ($type) {
                'mahanagarpalika',
                'metropolitan',
                'metropolitan city' => 'metropolitan',

                'upmahanagarpalika',
                'upamahanagarpalika',
                'sub-metropolitan',
                'sub metropolitan city' => 'sub_metropolitan',

                'nagarpalika',
                'municipality' => 'municipality',

                'gaunpalika',
                'rural municipality' => 'rural_municipality',

                default => $type,
            };

            $code = strtoupper(
                preg_replace(
                    '/[^A-Z0-9]+/',
                    '_',
                    $name
                )
            );

            $llgs[] = [
                'district_id' => $districts[$districtCode],
                'name' => $name,
                'type' => $type,
                'code' => $code,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Insert / Update
        |--------------------------------------------------------------------------
        */

        foreach ($llgs as $llg) {

            DB::table('llgs')->updateOrInsert(
                [
                    'code' => $llg['code'],
                ],
                [
                    'district_id' => $llg['district_id'],
                    'name' => $llg['name'],
                    'type' => $llg['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info(
            count($llgs) . ' LLGs seeded successfully.'
        );
    }

    private function districtCode(string $district): string
    {
        return match ($district) {

            'RUKUM EAST',
            'EASTERN RUKUM',
            'EAST RUKUM' => 'RUKUM_EAST',

            'RUKUM WEST',
            'WESTERN RUKUM',
            'WEST RUKUM' => 'RUKUM_WEST',

            'NAWALPARASI WEST',
            'PARASI',
            'NAWALPARASI' => 'NAWALPARASI_WEST',

            'KAVRE',
            'KAVREPALANCHOWK',
            'KAVREPALANCHOK' => 'KAVREPALANCHOK',

            'TERHATHUM',
            'TEHRATHUM' => 'TERHATHUM',

            'SINDHUPALCHOWK',
            'SINDHUPALCHOK' => 'SINDHUPALCHOK',

            default => $district,
        };
    }
}