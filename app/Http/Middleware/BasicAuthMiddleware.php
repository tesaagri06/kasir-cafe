<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        if (!$username || !$password) {
            return response()->json([
                'success' => false,
                'message' => 'Basic Auth credentials tidak ditemukan.',
            ], 401)->header('WWW-Authenticate', 'Basic realm="CafePOS API"');
        }

        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        auth('api')->login($user);

        return $next($request);
    }
}