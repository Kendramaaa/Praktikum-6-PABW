<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBanjir extends Model
{
    protected $table = 'laporan_banjir';

    protected $fillable = [
        'nama_pelapor',
        'no_telepon',
        'kecamatan',
        'lokasi',
        'tinggi_air',
        'keterangan',
        'status',
    ];

    /** Status penanganan laporan (urutannya = alur kerja petugas). */
    public const STATUS = ['baru', 'diproses', 'selesai'];

    /** 31 kecamatan di Kabupaten Bandung. */
    public const KECAMATAN = [
        'Arjasari', 'Baleendah', 'Banjaran', 'Bojongsoang', 'Cangkuang', 'Cicalengka',
        'Cikancung', 'Cilengkrang', 'Cileunyi', 'Cimaung', 'Cimenyan', 'Ciparay',
        'Ciwidey', 'Dayeuhkolot', 'Ibun', 'Katapang', 'Kertasari', 'Kutawaringin',
        'Majalaya', 'Margaasih', 'Margahayu', 'Nagreg', 'Pacet', 'Pameungpeuk',
        'Pangalengan', 'Paseh', 'Pasirjambu', 'Rancabali', 'Rancaekek', 'Solokanjeruk',
        'Soreang',
    ];

    /** Tingkat genangan berdasarkan tinggi air (cm): rendah, sedang, tinggi. */
    public function getTingkatAttribute(): string
    {
        return match (true) {
            $this->tinggi_air >= 100 => 'tinggi',
            $this->tinggi_air >= 40 => 'sedang',
            default => 'rendah',
        };
    }
}
