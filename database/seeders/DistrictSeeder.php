<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = DB::table('provinces')
            ->pluck('id', 'code');

        $districts = [

            // =========================================================
            // KOSHI PROVINCE - 14 DISTRICTS
            // =========================================================
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Taplejung',
                'code' => 'TAPLEJUNG',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Panchthar',
                'code' => 'PANCHTHAR',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Ilam',
                'code' => 'ILAM',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Jhapa',
                'code' => 'JHAPA',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Morang',
                'code' => 'MORANG',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Sunsari',
                'code' => 'SUNSARI',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Dhankuta',
                'code' => 'DHANKUTA',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Terhathum',
                'code' => 'TERHATHUM',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Sankhuwasabha',
                'code' => 'SANKHUWASABHA',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Bhojpur',
                'code' => 'BHOJPUR',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Solukhumbu',
                'code' => 'SOLUKHUMBU',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Okhaldhunga',
                'code' => 'OKHALDHUNGA',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Khotang',
                'code' => 'KHOTANG',
            ],
            [
                'province_id' => $provinces['KOSHI'],
                'name' => 'Udayapur',
                'code' => 'UDAYAPUR',
            ],

            // =========================================================
            // MADHESH PROVINCE - 8 DISTRICTS
            // =========================================================
            [
                'province_id' => $provinces['MADHESH'],
                'name' => 'Saptari',
                'code' => 'SAPTARI',
            ],
            [
                'province_id' => $provinces['MADHESH'],
                'name' => 'Siraha',
                'code' => 'SIRAHA',
            ],
            [
                'province_id' => $provinces['MADHESH'],
                'name' => 'Dhanusha',
                'code' => 'DHANUSHA',
            ],
            [
                'province_id' => $provinces['MADHESH'],
                'name' => 'Mahottari',
                'code' => 'MAHOTTARI',
            ],
            [
                'province_id' => $provinces['MADHESH'],
                'name' => 'Sarlahi',
                'code' => 'SARLAHI',
            ],
            [
                'province_id' => $provinces['MADHESH'],
                'name' => 'Rautahat',
                'code' => 'RAUTAHAT',
            ],
            [
                'province_id' => $provinces['MADHESH'],
                'name' => 'Bara',
                'code' => 'BARA',
            ],
            [
                'province_id' => $provinces['MADHESH'],
                'name' => 'Parsa',
                'code' => 'PARSA',
            ],

            // =========================================================
            // BAGMATI PROVINCE - 13 DISTRICTS
            // =========================================================
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Dolakha',
                'code' => 'DOLAKHA',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Ramechhap',
                'code' => 'RAMECHHAP',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Sindhuli',
                'code' => 'SINDHULI',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Rasuwa',
                'code' => 'RASUWA',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Dhading',
                'code' => 'DHADING',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Nuwakot',
                'code' => 'NUWAKOT',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Kathmandu',
                'code' => 'KATHMANDU',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Bhaktapur',
                'code' => 'BHAKTAPUR',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Lalitpur',
                'code' => 'LALITPUR',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Kavrepalanchok',
                'code' => 'KAVREPALANCHOK',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Sindhupalchok',
                'code' => 'SINDHUPALCHOK',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Makwanpur',
                'code' => 'MAKWANPUR',
            ],
            [
                'province_id' => $provinces['BAGMATI'],
                'name' => 'Chitwan',
                'code' => 'CHITWAN',
            ],

            // =========================================================
            // GANDAKI PROVINCE - 11 DISTRICTS
            // =========================================================
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Gorkha',
                'code' => 'GORKHA',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Manang',
                'code' => 'MANANG',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Mustang',
                'code' => 'MUSTANG',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Myagdi',
                'code' => 'MYAGDI',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Kaski',
                'code' => 'KASKI',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Lamjung',
                'code' => 'LAMJUNG',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Tanahun',
                'code' => 'TANAHUN',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Nawalpur',
                'code' => 'NAWALPUR',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Syangja',
                'code' => 'SYANGJA',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Parbat',
                'code' => 'PARBAT',
            ],
            [
                'province_id' => $provinces['GANDAKI'],
                'name' => 'Baglung',
                'code' => 'BAGLUNG',
            ],

            // =========================================================
            // LUMBINI PROVINCE - 12 DISTRICTS
            // =========================================================
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Rukum East',
                'code' => 'RUKUM_EAST',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Rolpa',
                'code' => 'ROLPA',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Pyuthan',
                'code' => 'PYUTHAN',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Gulmi',
                'code' => 'GULMI',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Arghakhanchi',
                'code' => 'ARGHAKHANCHI',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Palpa',
                'code' => 'PALPA',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Nawalparasi West',
                'code' => 'NAWALPARASI_WEST',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Rupandehi',
                'code' => 'RUPANDEHI',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Kapilvastu',
                'code' => 'KAPILVASTU',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Dang',
                'code' => 'DANG',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Banke',
                'code' => 'BANKE',
            ],
            [
                'province_id' => $provinces['LUMBINI'],
                'name' => 'Bardiya',
                'code' => 'BARDIYA',
            ],

            // =========================================================
            // KARNALI PROVINCE - 10 DISTRICTS
            // =========================================================
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Dolpa',
                'code' => 'DOLPA',
            ],
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Humla',
                'code' => 'HUMLA',
            ],
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Jumla',
                'code' => 'JUMLA',
            ],
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Kalikot',
                'code' => 'KALIKOT',
            ],
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Mugu',
                'code' => 'MUGU',
            ],
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Surkhet',
                'code' => 'SURKHET',
            ],
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Dailekh',
                'code' => 'DAILEKH',
            ],
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Jajarkot',
                'code' => 'JAJARKOT',
            ],
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Salyan',
                'code' => 'SALYAN',
            ],
            [
                'province_id' => $provinces['KARNALI'],
                'name' => 'Rukum West',
                'code' => 'RUKUM_WEST',
            ],

            // =========================================================
            // SUDURPASHCHIM PROVINCE - 9 DISTRICTS
            // =========================================================
            [
                'province_id' => $provinces['SUDURPASHCHIM'],
                'name' => 'Bajura',
                'code' => 'BAJURA',
            ],
            [
                'province_id' => $provinces['SUDURPASHCHIM'],
                'name' => 'Bajhang',
                'code' => 'BAJHANG',
            ],
            [
                'province_id' => $provinces['SUDURPASHCHIM'],
                'name' => 'Achham',
                'code' => 'ACHHAM',
            ],
            [
                'province_id' => $provinces['SUDURPASHCHIM'],
                'name' => 'Doti',
                'code' => 'DOTI',
            ],
            [
                'province_id' => $provinces['SUDURPASHCHIM'],
                'name' => 'Kailali',
                'code' => 'KAILALI',
            ],
            [
                'province_id' => $provinces['SUDURPASHCHIM'],
                'name' => 'Darchula',
                'code' => 'DARCHULA',
            ],
            [
                'province_id' => $provinces['SUDURPASHCHIM'],
                'name' => 'Baitadi',
                'code' => 'BAITADI',
            ],
            [
                'province_id' => $provinces['SUDURPASHCHIM'],
                'name' => 'Dadeldhura',
                'code' => 'DADELDHURA',
            ],
            [
                'province_id' => $provinces['SUDURPASHCHIM'],
                'name' => 'Kanchanpur',
                'code' => 'KANCHANPUR',
            ],
        ];

        foreach ($districts as $district) {
            DB::table('districts')->updateOrInsert(
                ['code' => $district['code']],
                [
                    'province_id' => $district['province_id'],
                    'name' => $district['name'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

