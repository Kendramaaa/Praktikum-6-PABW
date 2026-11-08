<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan form login (resources/views/auth/login.blade.php)
    public function create()
    {
        return view('auth.login');
    }

    // Memeriksa email & password, lalu membuat session login
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // attempt(): cari user berdasarkan email, lalu cocokkan password dengan hash-nya.
        // Parameter kedua = fitur "Remember Me".
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // ID session baru setelah login -> mencegah session fixation
            $request->session()->regenerate();

            // intended(): kembali ke halaman yang tadi dituju, atau Daftar Laporan jika tidak ada
            return redirect()->intended(route('laporan.index'));
        }

        return back()
            ->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])
            ->onlyInput('email');
    }

    // Logout: hapus status login, kosongkan session, ganti token CSRF
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
