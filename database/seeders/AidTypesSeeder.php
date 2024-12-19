<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AidTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aid_types')->insert([
            [
                'name' => 'Makanan',
                'description' => 'Bantuan berupa makanan',
                'category' => 'Kebutuhan Pokok',
                'unit' => 'kg',
                'priority_level' => 'tinggi',
                'is_perishable' => true,
                'storage_method' => 'Simpan di tempat sejuk',
                'distribution_method' => 'Langsung',
                'donor_name' => 'Donatur A',
                'donor_contact' => '08123456789',
                'donor_type' => 'organisasi',
                'donation_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Pakaian',
                'description' => 'Bantuan berupa pakaian',
                'category' => 'Sandang',
                'unit' => 'pcs',
                'priority_level' => 'sedang',
                'is_perishable' => false,
                'storage_method' => 'Simpan di tempat kering',
                'distribution_method' => 'Langsung',
                'donor_name' => 'Donatur B',
                'donor_contact' => '08123456790',
                'donor_type' => 'individu',
                'donation_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Obat-obatan',
                'description' => 'Bantuan berupa obat-obatan',
                'category' => 'Kesehatan',
                'unit' => 'box',
                'priority_level' => 'tinggi',
                'is_perishable' => true,
                'storage_method' => 'Simpan di tempat sejuk dan kering',
                'distribution_method' => 'Melalui tenaga medis',
                'donor_name' => 'Donatur C',
                'donor_contact' => '08123456791',
                'donor_type' => 'organisasi',
                'donation_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
