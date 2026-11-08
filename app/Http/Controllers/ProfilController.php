<?php

namespace App\Http\Controllers;

class ProfilController extends Controller
{
    // Halaman profil petugas yang sedang login (data diambil lewat Auth::user() di view)
    public function show()
    {
        return view('profil');
    }
}
