<?php

namespace App\Http\Controllers;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
class MahasiswaController extends Controller
{
    public function form()
    {
        return view('form');
    }

    public function simpan(Request $request)
    {
        Mahasiswa::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'jurusan' => $request->jurusan,
            'umur' => $request->umur,
        ]);

        return "Data Mahasiswa Berhasil Disimpan!";
    }

    public function daftar()
    {
        $data = Mahasiswa::all();
        return view('mahasiswa', ['mahasiswa' => $data]);
    }
}
