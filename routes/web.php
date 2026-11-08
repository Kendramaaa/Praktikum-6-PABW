<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LaporanBanjirController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik — dapat dibuka siapa saja, tanpa login
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Warga mengirim laporan banjir
Route::get('/lapor', [LaporanBanjirController::class, 'form'])->name('laporan.form');
Route::post('/lapor', [LaporanBanjirController::class, 'simpan'])->name('laporan.simpan');

/*
|--------------------------------------------------------------------------
| Authentication manual (tanpa Breeze)
|--------------------------------------------------------------------------
| Grup "guest": pengguna yang sudah login tidak perlu membuka form lagi.
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Nama route "login" wajib ada: middleware auth mengarahkan tamu ke route ini.
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Logout memakai POST agar tidak bisa dipicu hanya dengan membuka sebuah link
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Halaman petugas — dilindungi middleware auth
|--------------------------------------------------------------------------
| Belum login -> otomatis diarahkan ke /login.
*/
Route::middleware('auth')->group(function () {
    Route::get('/laporan', [LaporanBanjirController::class, 'index'])->name('laporan.index');
    Route::patch('/laporan/{laporan}/status', [LaporanBanjirController::class, 'ubahStatus'])->name('laporan.status');
    Route::delete('/laporan/{laporan}', [LaporanBanjirController::class, 'hapus'])->name('laporan.hapus');

    Route::get('/profil', [ProfilController::class, 'show'])->name('profil');

    // Sisa Modul 4 (data mahasiswa) berisi data pribadi, jadi daftarnya ikut dilindungi
    Route::get('/daftar-mahasiswa', [MahasiswaController::class, 'daftar']);
});

/*
|--------------------------------------------------------------------------
| Sisa Modul 4 (CRUD Mahasiswa) — tetap dipertahankan
|--------------------------------------------------------------------------
*/
Route::get('/form', [MahasiswaController::class, 'form']);
Route::post('/simpan', [MahasiswaController::class, 'simpan']);
