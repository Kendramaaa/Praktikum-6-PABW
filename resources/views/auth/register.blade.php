@extends('layouts.app')

@section('title', 'Register Petugas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card">
            <div class="card-header">Daftar akun petugas</div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf {{-- Token keamanan: form hanya bisa dikirim dari aplikasi ini --}}

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required autocomplete="name" autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required autocomplete="new-password" aria-describedby="passwordHelp">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div id="passwordHelp" class="form-text">Minimal 8 karakter.</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password-confirm" class="form-label">Ulangi password</label>
                        {{-- Nama input harus password_confirmation agar lolos aturan "confirmed" --}}
                        <input id="password-confirm" type="password" name="password_confirmation"
                            class="form-control" required autocomplete="new-password">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Buat akun</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    </div>
</div>
@endsection
