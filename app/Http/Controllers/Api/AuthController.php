<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // POST /api/auth/register
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'username'      => $request->username,
            'password_hash' => Hash::make($request->password),
            'nama_lengkap'  => $request->nama_lengkap,
            'email'         => $request->email,
            'telepon'       => $request->telepon,
            'role'          => 'customer',
            'api_key'       => Str::random(64),
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'data'    => [
                'user'  => $this->formatUser($user),
                'token' => $this->formatToken($token),
            ],
        ], 201);
    }

    // POST /api/auth/login
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user'  => $this->formatUser($user),
                'token' => $this->formatToken($token),
            ],
        ]);
    }

    // POST /api/auth/logout
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    // POST /api/auth/refresh
    public function refresh(): JsonResponse
    {
        try {
            $token = auth('api')->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil diperbarui.',
                'data'    => [
                    'token' => $this->formatToken($token),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah kedaluwarsa.',
            ], 401);
        }
    }

    // GET /api/auth/me
    public function me(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'user' => $this->formatUser(auth('api')->user()),
            ],
        ]);
    }
    private function formatUser(User $user): array
    {
        return [
            'id'           => $user->id_user,
            'username'     => $user->username,
            'nama_lengkap' => $user->nama_lengkap,
            'email'        => $user->email,
            'telepon'      => $user->telepon,
            'role'         => $user->role,
        ];
    }

    private function formatToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ];
    }
}