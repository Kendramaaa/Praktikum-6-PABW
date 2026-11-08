@extends('layouts.app')

@section('title', 'Profil Petugas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <h1 class="page-title mb-3">Profil petugas</h1>
        <div class="card">
            <div class="card-body">
                {{-- Auth::user() = baris tabel users milik pengguna yang sedang login --}}
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8">{{ Auth::user()->name }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ Auth::user()->email }}</dd>

                    <dt class="col-sm-4">Akun dibuat</dt>
                    <dd class="col-sm-8 mb-0">
                        {{ Auth::user()->created_at->isoFormat('D MMMM YYYY, HH:mm') }} WIB
                        <span class="text-secondary">({{ Auth::user()->created_at->diffForHumans() }})</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
