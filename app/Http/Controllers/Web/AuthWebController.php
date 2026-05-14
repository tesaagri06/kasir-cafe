<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return $this->redirectByRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return back()->with('error', 'Username atau password salah.');
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return $this->redirectByRole();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username'     => 'required|string|min:3|max:50|unique:users,username',
            'nama_lengkap' => 'required|string|max:100',
            'email'        => 'nullable|email|unique:users,email',
            'password'     => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'username'      => $request->username,
            'nama_lengkap'  => $request->nama_lengkap,
            'email'         => $request->email,
            'password_hash' => Hash::make($request->password),
            'role'          => 'customer',
            'api_key'       => Str::random(64),
        ]);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('transaksi.create');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole()
    {
        $user = Auth::guard('web')->user();
        if ($user->isKasir() || $user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('transaksi.create');
    }
}