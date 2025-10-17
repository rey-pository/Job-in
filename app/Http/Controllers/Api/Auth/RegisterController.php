<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|in:2,3',
        ]);

        $status_verifikasi = ($validated['role_id'] == 3) ? 'approved' : 'pending';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'status_verifikasi' => $status_verifikasi,
        ]);

        event(new Registered($user));

        if ($user->status_verifikasi === 'approved') {
            Auth::login($user);
            return redirect('/dashboard');
        }

        return redirect()->route('login.form')->with('status', 'Pendaftaran perusahaan berhasil! Akun Anda akan aktif setelah disetujui oleh admin.');
    }
}