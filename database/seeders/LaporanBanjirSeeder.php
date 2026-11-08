<?php

namespace Database\Seeders;

use App\Models\LaporanBanjir;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class LaporanBanjirSeeder extends Seeder
{
    /**
     * Laporan contoh agar halaman Daftar Laporan tidak kosong saat demo.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Kecamatan di sekitar Daerah Aliran Sungai Citarum yang langganan banjir
        $kecamatan = ['Dayeuhkolot', 'Baleendah', 'Bojongsoang', 'Rancaekek', 'Majalaya', 'Banjaran', 'Cicalengka'];

        for ($i = 0; $i < 12; $i++) {
            $waktu = now()->subHours($faker->numberBetween(1, 160));

            LaporanBanjir::create([
                'nama_pelapor' => $faker->name,
                'no_telepon' => '08' . $faker->numerify('##########'),
                'kecamatan' => $faker->randomElement($kecamatan),
                'lokasi' => 'Jl. ' . $faker->streetName . ' RT ' . $faker->numerify('0#') . '/RW ' . $faker->numerify('0#'),
                'tinggi_air' => $faker->randomElement([15, 20, 30, 45, 50, 70, 90, 110, 130, 150]),
                'keterangan' => $faker->optional(0.7)->randomElement([
                    'Air masuk ke rumah warga.',
                    'Jalan utama tidak bisa dilewati kendaraan roda dua.',
                    'Ada lansia yang perlu dievakuasi.',
                    'Air naik cepat sejak dini hari.',
                    'Saluran drainase meluap.',
                ]),
                'status' => $faker->randomElement(['baru', 'baru', 'diproses', 'diproses', 'selesai']),
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);
        }
    }
}
