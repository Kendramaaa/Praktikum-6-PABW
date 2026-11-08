@extends('layouts.app')

@section('title', 'Daftar Laporan')

@section('content')
@php
    $total = $jumlah->sum();
    $filter = [
        ['label' => 'Semua', 'status' => null, 'count' => $total],
        ['label' => 'Baru', 'status' => 'baru', 'count' => $jumlah['baru'] ?? 0],
        ['label' => 'Diproses', 'status' => 'diproses', 'count' => $jumlah['diproses'] ?? 0],
        ['label' => 'Selesai', 'status' => 'selesai', 'count' => $jumlah['selesai'] ?? 0],
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <h1 class="page-title mb-1">Daftar laporan banjir</h1>
        <p class="text-secondary mb-0">Halo, {{ Auth::user()->name }}. Laporan terbaru tampil paling atas.</p>
    </div>
</div>

{{-- Filter status; angka = jumlah laporan pada status tersebut --}}
<ul class="nav nav-tabs mb-3">
    @foreach ($filter as $f)
        <li class="nav-item">
            <a class="nav-link {{ $statusAktif === $f['status'] ? 'active' : '' }}"
               href="{{ route('laporan.index', $f['status'] ? ['status' => $f['status']] : []) }}">
                {{ $f['label'] }} <span class="text-secondary">({{ $f['count'] }})</span>
            </a>
        </li>
    @endforeach
</ul>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Waktu</th>
                    <th scope="col">Pelapor</th>
                    <th scope="col">Lokasi</th>
                    <th scope="col">Tinggi air</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">Status</th>
                    <th scope="col"><span class="visually-hidden">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporan as $item)
                    <tr>
                        <td class="text-nowrap">
                            {{ $item->created_at->isoFormat('D MMM, HH:mm') }}
                            <div class="small text-secondary">{{ $item->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            {{ $item->nama_pelapor }}
                            <div class="small"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $item->no_telepon) }}">{{ $item->no_telepon }}</a></div>
                        </td>
                        <td>
                            {{ $item->lokasi }}
                            <div class="small text-secondary">Kec. {{ $item->kecamatan }}</div>
                        </td>
                        <td class="text-nowrap">
                            {{ $item->tinggi_air }} cm
                            <div><span class="tag tag-{{ $item->tingkat }}">{{ ucfirst($item->tingkat) }}</span></div>
                        </td>
                        <td style="min-width: 14rem;">{{ $item->keterangan ?: '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('laporan.status', $item) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" style="min-width: 7.5rem;"
                                    aria-label="Status laporan {{ $item->nama_pelapor }}" onchange="this.form.submit()">
                                    @foreach (\App\Models\LaporanBanjir::STATUS as $s)
                                        <option value="{{ $s }}" @selected($item->status === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('laporan.hapus', $item) }}"
                                onsubmit="return confirm('Hapus laporan dari {{ e($item->nama_pelapor) }}? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Hapus laporan {{ $item->nama_pelapor }}">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-5">
                            @if ($statusAktif)
                                Tidak ada laporan dengan status "{{ $statusAktif }}".
                            @else
                                Belum ada laporan masuk. Laporan dari warga akan muncul di sini.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($laporan->hasPages())
    <div class="mt-3">{{ $laporan->links() }}</div>
@endif
@endsection
