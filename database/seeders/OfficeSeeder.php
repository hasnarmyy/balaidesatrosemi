<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('offices')->insert([
            'name' => 'Kantor Pusat',
            'address' => 'Alamat Kantor Pusat',
            'latitude' => -7.5864868248927,
            'longitude' => 110.74897440665,
            'radius_meters' => 200,
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
