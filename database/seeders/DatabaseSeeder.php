<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan: php artisan db:seed
     */
    public function run(): void
    {
        $this->call([
            PetugasSeeder::class,       // 5 petugas dummy + 1 admin BPBD
            LaporanBanjirSeeder::class, // 12 laporan contoh
        ]);
    }
}
