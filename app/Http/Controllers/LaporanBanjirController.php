<?php

namespace App\Http\Controllers;

use App\Models\LaporanBanjir;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaporanBanjirController extends Controller
{
    /* ---------- Halaman publik (tanpa login) ---------- */

    // Form pelaporan untuk warga
    public function form()
    {
        return view('laporan.form', ['daftarKecamatan' => LaporanBanjir::KECAMATAN]);
    }

    // Menyimpan laporan dari warga
    public function simpan(Request $request)
    {
        $data = $request->validate([
            'nama_pelapor' => ['required', 'string', 'max:255'],
            'no_telepon' => ['required', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'kecamatan' => ['required', Rule::in(LaporanBanjir::KECAMATAN)],
            'lokasi' => ['required', 'string', 'max:255'],
            'tinggi_air' => ['required', 'integer', 'min:1', 'max:500'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], [
            'nama_pelapor.required' => 'Nama pelapor wajib diisi.',
            'no_telepon.required' => 'Nomor telepon wajib diisi agar petugas dapat menghubungi Anda.',
            'no_telepon.regex' => 'Nomor telepon hanya boleh berisi angka (8–20 digit), contoh: 081234567890.',
            'kecamatan.required' => 'Pilih kecamatan lokasi banjir.',
            'kecamatan.in' => 'Kecamatan yang dipilih tidak dikenal.',
            'lokasi.required' => 'Alamat atau lokasi banjir wajib diisi.',
            'tinggi_air.required' => 'Perkiraan tinggi air wajib diisi.',
            'tinggi_air.integer' => 'Tinggi air harus berupa angka bulat dalam sentimeter.',
            'tinggi_air.min' => 'Tinggi air minimal 1 cm.',
            'tinggi_air.max' => 'Tinggi air maksimal 500 cm.',
        ]);

        LaporanBanjir::create($data); // status otomatis "baru"

        return redirect()->route('laporan.form')
            ->with('success', 'Laporan terkirim. Petugas BPBD akan memeriksa laporan Anda.');
    }

    /* ---------- Halaman petugas (dilindungi middleware auth) ---------- */

    // Daftar laporan, bisa difilter berdasarkan status
    public function index(Request $request)
    {
        $status = $request->query('status');

        $laporan = LaporanBanjir::query()
            ->when(in_array($status, LaporanBanjir::STATUS, true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $jumlah = LaporanBanjir::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('laporan.index', [
            'laporan' => $laporan,
            'jumlah' => $jumlah,
            'statusAktif' => in_array($status, LaporanBanjir::STATUS, true) ? $status : null,
        ]);
    }

    // Mengubah status penanganan laporan
    public function ubahStatus(Request $request, LaporanBanjir $laporan)
    {
        $request->validate(['status' => ['required', Rule::in(LaporanBanjir::STATUS)]]);

        $laporan->update(['status' => $request->status]);

        return back()->with('success', 'Status laporan dari ' . $laporan->nama_pelapor . ' diubah menjadi "' . $laporan->status . '".');
    }

    // Menghapus laporan
    public function hapus(LaporanBanjir $laporan)
    {
        $laporan->delete();

        return back()->with('success', 'Laporan dari ' . $laporan->nama_pelapor . ' dihapus.');
    }
}
