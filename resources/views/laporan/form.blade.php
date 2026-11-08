@extends('layouts.app')

@section('title', 'Kirim Laporan Banjir')

@push('styles')
<style>
    .gauge-box { position: sticky; top: 1rem; }
    .gauge { position: relative; height: 300px; width: 64px; border: 2px solid var(--ink); border-top: 0; border-radius: 0 0 .35rem .35rem; background: #fff; overflow: hidden; }
    .gauge-water { position: absolute; left: 0; right: 0; bottom: 0; height: 0; background: linear-gradient(180deg, #5FB4D3, var(--water)); transition: height .35s ease; }
    .gauge-labels { position: relative; height: 300px; flex: 1; }
    .gauge-labels .mark { position: absolute; left: 0; right: 0; transform: translateY(50%); display: flex; align-items: center; gap: .5rem; font-size: .9rem; }
    .gauge-labels .mark::before { content: ""; width: 1.25rem; border-top: 2px solid var(--ink); }
    .gauge-labels .mark b { font-weight: 600; }
    @media (prefers-reduced-motion: reduce) { .gauge-water { transition: none; } }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <h1 class="page-title mb-1">Kirim laporan banjir</h1>
        <p class="text-secondary mb-4">Tanpa login. Petugas BPBD akan menghubungi nomor telepon Anda bila diperlukan.</p>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('laporan.simpan') }}" novalidate>
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama_pelapor" class="form-label">Nama pelapor</label>
                            <input id="nama_pelapor" type="text" name="nama_pelapor" value="{{ old('nama_pelapor') }}"
                                class="form-control @error('nama_pelapor') is-invalid @enderror" required autocomplete="name">
                            @error('nama_pelapor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="no_telepon" class="form-label">Nomor telepon</label>
                            <input id="no_telepon" type="tel" name="no_telepon" value="{{ old('no_telepon') }}"
                                class="form-control @error('no_telepon') is-invalid @enderror" required autocomplete="tel"
                                placeholder="081234567890">
                            @error('no_telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-5">
                            <label for="kecamatan" class="form-label">Kecamatan</label>
                            <select id="kecamatan" name="kecamatan" class="form-select @error('kecamatan') is-invalid @enderror" required>
                                <option value="" disabled @selected(! old('kecamatan'))>Pilih kecamatan</option>
                                @foreach ($daftarKecamatan as $kec)
                                    <option value="{{ $kec }}" @selected(old('kecamatan') === $kec)>{{ $kec }}</option>
                                @endforeach
                            </select>
                            @error('kecamatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-7">
                            <label for="lokasi" class="form-label">Alamat lokasi banjir</label>
                            <input id="lokasi" type="text" name="lokasi" value="{{ old('lokasi') }}"
                                class="form-control @error('lokasi') is-invalid @enderror" required
                                placeholder="Nama jalan, RT/RW, desa">
                            @error('lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-5">
                            <label for="tinggi_air" class="form-label">Perkiraan tinggi air</label>
                            <div class="input-group has-validation">
                                <input id="tinggi_air" type="number" inputmode="numeric" min="1" max="500" name="tinggi_air"
                                    value="{{ old('tinggi_air') }}"
                                    class="form-control @error('tinggi_air') is-invalid @enderror" required
                                    aria-describedby="tinggiHelp">
                                <span class="input-group-text">cm</span>
                                @error('tinggi_air') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div id="tinggiHelp" class="form-text" aria-live="polite">Lihat penggaris di samping.</div>
                        </div>

                        <div class="col-12">
                            <label for="keterangan" class="form-label">Keterangan <span class="text-secondary fw-normal">(boleh dikosongkan)</span></label>
                            <textarea id="keterangan" name="keterangan" rows="3"
                                class="form-control @error('keterangan') is-invalid @enderror"
                                placeholder="Contoh: ada warga lansia yang perlu dievakuasi">{{ old('keterangan') }}</textarea>
                            @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">Kirim laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Penggaris tinggi air: membantu warga memperkirakan tinggi air dengan anggota tubuh --}}
    <div class="col-lg-5">
        <div class="gauge-box">
            <h2 class="h6 mb-3">Perkirakan dengan tubuh Anda</h2>
            <div class="d-flex gap-3">
                <div class="gauge" aria-hidden="true"><div class="gauge-water" id="gaugeWater"></div></div>
                <div class="gauge-labels" aria-hidden="true">
                    <div class="mark" style="bottom: 6.67%"><b>Mata kaki</b> sekitar 10 cm</div>
                    <div class="mark" style="bottom: 33.33%"><b>Lutut</b> sekitar 50 cm</div>
                    <div class="mark" style="bottom: 66.67%"><b>Pinggang</b> sekitar 100 cm</div>
                    <div class="mark" style="bottom: 86.67%"><b>Dada</b> sekitar 130 cm</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tinggi air yang diketik langsung tergambar pada penggaris (skala 0–150 cm)
    (function () {
        var input = document.getElementById('tinggi_air');
        var water = document.getElementById('gaugeWater');
        var help = document.getElementById('tinggiHelp');
        var MAX = 150;

        function posisi(cm) {
            if (cm < 10) return 'di bawah mata kaki';
            if (cm < 50) return 'antara mata kaki dan lutut';
            if (cm < 100) return 'antara lutut dan pinggang';
            if (cm < 130) return 'antara pinggang dan dada';
            return 'setinggi dada atau lebih';
        }

        function update() {
            var cm = parseInt(input.value, 10);
            if (isNaN(cm) || cm < 1) {
                water.style.height = '0';
                help.textContent = 'Lihat penggaris di samping.';
                return;
            }
            water.style.height = Math.min(cm, MAX) / MAX * 100 + '%';
            help.textContent = 'Kira-kira ' + posisi(cm) + '.';
        }

        input.addEventListener('input', update);
        update();
    })();
</script>
@endpush
