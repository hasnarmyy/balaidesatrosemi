<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Database\Seeders\OfficeSeeder;
use Database\Seeders\FaceSampleSeeder;
use Database\Seeders\SipegSqlSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Import data from sipeg.sql (INSERT statements)
        $this->call(SipegSqlSeeder::class);

        // Seed default office and placeholder face-sample seeder
        $this->call(OfficeSeeder::class);
        $this->call(FaceSampleSeeder::class);
    }
}
