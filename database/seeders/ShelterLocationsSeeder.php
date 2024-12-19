<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShelterLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shelter_locations')->insert([
            [
                'name' => 'GOR Juwana',
                'capacity' => 200,
                'facilities' => 'Tempat tidur, dapur umum, toilet, ruang kesehatan, area parkir luas',
                'latitude' => -6.7001,
                'longitude' => 111.1468,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Gedung SMAN 1 Gembong',
                'capacity' => 150,
                'facilities' => 'Tempat tidur, dapur umum, toilet, ruang kesehatan, lapangan olahraga',
                'latitude' => -6.6412,
                'longitude' => 110.9388,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Balai Desa Wedarijaksa',
                'capacity' => 100,
                'facilities' => 'Tempat tidur, dapur umum, toilet, ruang kesehatan, gudang logistik',
                'latitude' => -6.6589,
                'longitude' => 111.0622,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
