@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="row align-items-center g-4 py-lg-4">
    <div class="col-lg-7">
        <h1 class="display-5 fw-bold mb-3">Air naik di lingkungan Anda? Laporkan sekarang.</h1>
        <p class="lead text-secondary mb-4" style="max-width: 36rem;">
            Laporan Anda langsung masuk ke petugas BPBD Kabupaten Bandung.
            Tidak perlu membuat akun — cukup isi lokasi dan perkiraan tinggi air.
        </p>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('laporan.form') }}" class="btn btn-primary btn-lg">Kirim laporan banjir</a>
            @auth
                <a href="{{ route('laporan.index') }}" class="btn btn-outline-primary btn-lg">Buka daftar laporan</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">Masuk sebagai petugas</a>
            @endauth
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Setelah laporan dikirim</div>
            <ol class="list-group list-group-numbered list-group-flush">
                <li class="list-group-item">Laporan tercatat dengan status <strong>baru</strong>.</li>
                <li class="list-group-item">Petugas memeriksa dan mengubah status menjadi <strong>diproses</strong>.</li>
                <li class="list-group-item">Setelah ditangani, status menjadi <strong>selesai</strong>.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
