<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AidDistributionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil semua ID dari disaster_locations
        $disasters = DB::table('disaster_locations')->get();
        if ($disasters->isEmpty()) {
            throw new \Exception('Tidak ada data disaster_locations. Jalankan DisasterLocationsSeeder terlebih dahulu.');
        }

        // Ambil semua ID dari shelter_locations
        $shelters = DB::table('shelter_locations')->get();
        if ($shelters->isEmpty()) {
            throw new \Exception('Tidak ada data shelter_locations. Jalankan ShelterLocationsSeeder terlebih dahulu.');
        }

        // Ambil ID dari aid_types
        $aidTypes = DB::table('aid_types')->pluck('id', 'name');
        if ($aidTypes->isEmpty()) {
            throw new \Exception('Tidak ada data aid_types. Jalankan AidTypesSeeder terlebih dahulu.');
        }

        DB::table('aid_distributions')->insert([
            [
                'disaster_location_id' => $disasters[0]->id, // Banjir di Juwana
                'shelter_location_id' => $shelters[0]->id,   // GOR Juwana (terdekat)
                'aid_type_id' => $aidTypes['Makanan'],
                'quantity' => 150,
                'description' => 'Distribusi bantuan makanan untuk korban banjir',
                'date' => '2023-12-19',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'disaster_location_id' => $disasters[1]->id, // Tanah Longsor di Gembong
                'shelter_location_id' => $shelters[1]->id,   // SMAN 1 Gembong (terdekat)
                'aid_type_id' => $aidTypes['Pakaian'],
                'quantity' => 200,
                'description' => 'Distribusi bantuan pakaian untuk korban longsor',
                'date' => '2023-12-21',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'disaster_location_id' => $disasters[2]->id, // Angin Puting Beliung di Wedarijaksa
                'shelter_location_id' => $shelters[2]->id,   // Balai Desa Wedarijaksa (terdekat)
                'aid_type_id' => $aidTypes['Obat-obatan'],
                'quantity' => 100,
                'description' => 'Distribusi bantuan obat-obatan untuk korban angin puting beliung',
                'date' => '2023-12-26',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
