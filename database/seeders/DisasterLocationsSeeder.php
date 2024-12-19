<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DisasterLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('disaster_locations')->insert([
            [
                'type' => 'Banjir',
                'location' => 'Kecamatan Juwana, Pati',
                'description' => 'Banjir akibat luapan Sungai Juwana setinggi 1.5 meter',
                'date' => '2023-12-15',
                'severity' => 3,
                'latitude' => -6.7113,
                'longitude' => 111.1521,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'type' => 'Tanah Longsor',
                'location' => 'Kecamatan Gembong, Pati',
                'description' => 'Longsor di area perbukitan setelah hujan deras',
                'date' => '2023-12-20',
                'severity' => 4,
                'latitude' => -6.6547,
                'longitude' => 110.9294,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'type' => 'Angin Puting Beliung',
                'location' => 'Kecamatan Wedarijaksa, Pati',
                'description' => 'Angin kencang merusak puluhan rumah warga',
                'date' => '2023-12-25',
                'severity' => 3,
                'latitude' => -6.6642,
                'longitude' => 111.0577,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
