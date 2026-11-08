<?php

namespace Tests\Feature;

use App\Models\LaporanBanjir;
use App\Models\User;
use Database\Seeders\PetugasSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LaporBanjirAuthTest extends TestCase
{
    use RefreshDatabase;

    private function dataLaporan(array $override = []): array
    {
        return array_merge([
            'nama_pelapor' => 'Siti Aminah',
            'no_telepon' => '081234567890',
            'kecamatan' => 'Dayeuhkolot',
            'lokasi' => 'Jl. Sukabirus RT 02/RW 05',
            'tinggi_air' => 60,
            'keterangan' => 'Air masuk ke rumah.',
        ], $override);
    }

    /* ---------- Proteksi halaman ---------- */

    public function test_tamu_diarahkan_ke_login_saat_membuka_daftar_laporan(): void
    {
        $this->get('/laporan')->assertRedirect('/login');
        $this->get('/profil')->assertRedirect('/login');
        $this->get('/daftar-mahasiswa')->assertRedirect('/login');
    }

    public function test_tamu_tidak_bisa_mengubah_atau_menghapus_laporan(): void
    {
        $laporan = LaporanBanjir::create($this->dataLaporan());

        $this->patch("/laporan/{$laporan->id}/status", ['status' => 'selesai'])->assertRedirect('/login');
        $this->delete("/laporan/{$laporan->id}")->assertRedirect('/login');

        $this->assertDatabaseHas('laporan_banjir', ['id' => $laporan->id, 'status' => 'baru']);
    }

    public function test_form_pelaporan_tetap_terbuka_tanpa_login(): void
    {
        $this->get('/')->assertOk();
        $this->get('/lapor')->assertOk()->assertSee('Kirim laporan banjir');
    }

    /* ---------- Pelaporan oleh warga ---------- */

    public function test_warga_bisa_mengirim_laporan_tanpa_login(): void
    {
        $this->post('/lapor', $this->dataLaporan())
            ->assertRedirect('/lapor')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('laporan_banjir', ['nama_pelapor' => 'Siti Aminah', 'status' => 'baru']);
    }

    public function test_laporan_tidak_valid_ditolak(): void
    {
        $this->post('/lapor', $this->dataLaporan(['nama_pelapor' => '', 'tinggi_air' => 'abc', 'kecamatan' => 'Gotham']))
            ->assertSessionHasErrors(['nama_pelapor', 'tinggi_air', 'kecamatan']);

        $this->assertDatabaseCount('laporan_banjir', 0);
    }

    /* ---------- Register ---------- */

    public function test_register_membuat_akun_ber_hash_dan_langsung_login(): void
    {
        $this->get('/register')->assertOk();

        $this->post('/register', [
            'name' => 'Budi Petugas',
            'email' => 'budi@bpbd.example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('laporan.index'));

        $this->assertAuthenticated();

        $user = User::where('email', 'budi@bpbd.example.com')->firstOrFail();
        $this->assertNotSame('rahasia123', $user->password);
        $this->assertStringStartsWith('$2y$', $user->password); // bcrypt
        $this->assertTrue(Hash::check('rahasia123', $user->password));
    }

    public function test_register_gagal_jika_data_tidak_valid(): void
    {
        User::factory()->create(['email' => 'sudah@ada.com']);

        $this->post('/register', [
            'name' => '',
            'email' => 'sudah@ada.com',
            'password' => 'pendek',
            'password_confirmation' => 'beda',
        ])->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertGuest();
    }

    /* ---------- Login & logout ---------- */

    public function test_login_berhasil_dan_bisa_membuka_daftar_laporan(): void
    {
        User::factory()->create(['email' => 'petugas@bpbd.example.com', 'password' => Hash::make('rahasia123')]);
        LaporanBanjir::create($this->dataLaporan());

        $this->get('/login')->assertOk();

        $this->post('/login', ['email' => 'petugas@bpbd.example.com', 'password' => 'rahasia123'])
            ->assertRedirect(route('laporan.index'));

        $this->assertAuthenticated();
        $this->get('/laporan')->assertOk()->assertSee('Siti Aminah');
    }

    public function test_login_gagal_menampilkan_pesan_galat(): void
    {
        User::factory()->create(['email' => 'petugas@bpbd.example.com', 'password' => Hash::make('rahasia123')]);

        $this->from('/login')
            ->post('/login', ['email' => 'petugas@bpbd.example.com', 'password' => 'salah-total'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'Email atau password yang Anda masukkan salah.']);

        $this->assertGuest();
    }

    public function test_setelah_login_pengguna_kembali_ke_halaman_yang_dituju(): void
    {
        User::factory()->create(['email' => 'petugas@bpbd.example.com', 'password' => Hash::make('rahasia123')]);

        $this->get('/profil')->assertRedirect('/login');
        $this->post('/login', ['email' => 'petugas@bpbd.example.com', 'password' => 'rahasia123'])
            ->assertRedirect('/profil');
    }

    public function test_pengguna_yang_sudah_login_tidak_perlu_membuka_form_login(): void
    {
        $this->actingAs(User::factory()->create())->get('/login')->assertRedirect(route('laporan.index'));
    }

    public function test_logout_menghapus_session(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/logout')->assertRedirect('/');

        $this->assertGuest();
        $this->get('/laporan')->assertRedirect('/login');
    }

    /* ---------- Navigasi & profil ---------- */

    public function test_menu_navigasi_berbeda_untuk_tamu_dan_petugas(): void
    {
        $this->get('/')
            ->assertSee('Login')->assertSee('Register')
            ->assertDontSee('Daftar Laporan')->assertDontSee('Profil Petugas')->assertDontSee('Logout');

        $this->actingAs(User::factory()->create())->get('/')
            ->assertSee('Daftar Laporan')->assertSee('Profil Petugas')->assertSee('Logout')
            ->assertDontSee('>Register<', false);
    }

    public function test_profil_menampilkan_data_petugas_yang_login(): void
    {
        $user = User::factory()->create(['name' => 'Rina Petugas', 'email' => 'rina@bpbd.example.com']);

        $this->actingAs($user)->get('/profil')
            ->assertOk()->assertSee('Rina Petugas')->assertSee('rina@bpbd.example.com');
    }

    /* ---------- Pengelolaan laporan ---------- */

    public function test_petugas_bisa_mengubah_status_dan_menghapus_laporan(): void
    {
        $laporan = LaporanBanjir::create($this->dataLaporan());
        $this->actingAs(User::factory()->create());

        $this->patch("/laporan/{$laporan->id}/status", ['status' => 'diproses'])->assertSessionHas('success');
        $this->assertDatabaseHas('laporan_banjir', ['id' => $laporan->id, 'status' => 'diproses']);

        $this->patch("/laporan/{$laporan->id}/status", ['status' => 'ngawur'])->assertSessionHasErrors('status');

        $this->delete("/laporan/{$laporan->id}")->assertSessionHas('success');
        $this->assertDatabaseMissing('laporan_banjir', ['id' => $laporan->id]);
    }

    public function test_filter_status_pada_daftar_laporan(): void
    {
        LaporanBanjir::create($this->dataLaporan(['nama_pelapor' => 'Pelapor Baru']));
        LaporanBanjir::create($this->dataLaporan(['nama_pelapor' => 'Pelapor Selesai', 'status' => 'selesai']));

        $this->actingAs(User::factory()->create())
            ->get('/laporan?status=selesai')
            ->assertSee('Pelapor Selesai')->assertDontSee('Pelapor Baru');
    }

    /* ---------- Seeder ---------- */

    public function test_petugas_seeder_membuat_6_akun_ber_hash_dan_bisa_login(): void
    {
        $this->seed(PetugasSeeder::class);

        $this->assertSame(6, User::count());

        foreach (User::all() as $user) {
            $this->assertStringStartsWith('$2y$', $user->password);
        }

        $this->post('/login', ['email' => 'admin@bpbd.example.com', 'password' => 'admin123'])
            ->assertRedirect(route('laporan.index'));
        $this->assertAuthenticated();
    }

    public function test_petugas_seeder_aman_dijalankan_dua_kali(): void
    {
        $this->seed(PetugasSeeder::class);
        $this->seed(PetugasSeeder::class);

        $this->assertSame(1, User::where('email', 'admin@bpbd.example.com')->count());
    }

    public function test_database_seeder_menjalankan_semua_seeder(): void
    {
        $this->seed();

        $this->assertSame(6, User::count());
        $this->assertSame(12, LaporanBanjir::count());
    }
}
