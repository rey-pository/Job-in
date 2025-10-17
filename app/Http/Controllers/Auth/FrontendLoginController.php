<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FrontendLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login-register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->login)
                    ->orWhere('phone_number', $request->login)
                    ->first();

        if ($user && Hash::check($request->password, $user->password)) {

            if ($user->role_id == 2 && $user->status_verifikasi !== 'approved') {
                 return back()->withErrors([
                    'login' => 'Akun perusahaan Anda sedang menunggu persetujuan admin.',
                ])->onlyInput('login');
            }

            Auth::login($user);
            $request->session()->regenerate();
            
            switch ($user->role_id) {
                case 1:
                    return redirect()->intended('/dashboard/admin');
                case 2:
                    return redirect()->intended('/dashboard/corporate');
                case 3:
                    return redirect()->intended('/dashboard/jobseeker');
                default:
                    Auth::logout();
                    return redirect('/login')->withErrors(['role' => 'Invalid role.']);
            }
        }

        return back()->withErrors([
            'login' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}