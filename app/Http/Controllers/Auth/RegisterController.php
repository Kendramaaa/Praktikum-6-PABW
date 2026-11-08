<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    // Menampilkan form register (resources/views/auth/register.blade.php)
    public function create()
    {
        return view('auth.register');
    }

    // Memvalidasi input, menyimpan akun petugas, lalu langsung login
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain atau masuk dengan akun tersebut.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama dengan password.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // Hash::make: password tidak pernah disimpan sebagai teks asli
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Setelah mendaftar, pengguna langsung dianggap login
        Auth::login($user);

        return redirect()->route('laporan.index')
            ->with('success', 'Akun petugas berhasil dibuat. Selamat datang, ' . $user->name . '.');
    }
}
