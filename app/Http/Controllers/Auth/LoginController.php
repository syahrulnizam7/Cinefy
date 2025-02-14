<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        // Cek apakah pengguna sudah ada berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Jika pengguna baru, buat akun baru
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => null,
                'profile_photo' => null,
                'username' => null,
                'google_id' => $googleUser->getId(),
            ]);

            Auth::login($user, true);

            // Jika pengguna baru, arahkan ke halaman welcome
            return redirect()->route('welcome');
        } else {
            // Jika pengguna lama tetapi belum memiliki google_id, update google_id
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user, true);

            // Jika pengguna sudah ada, arahkan ke halaman utama
            return redirect('/');
        }
    }
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba login
        if (Auth::attempt($request->only('username', 'password'), $request->remember)) {
            // Jika sukses, redirect ke halaman utama
            return redirect()->intended('/');
        }

        // Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username', 'remember'));
    }
}
