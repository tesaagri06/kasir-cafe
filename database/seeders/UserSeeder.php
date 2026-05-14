<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Seed akun awal persis seperti di kasir_cafe.sql asli lo.
     *
     * Semua password = "password"
     * API key di-generate otomatis per user saat seed.
     *
     * Kenapa pakai DB::table() bukan User::create()?
     * Karena seeder jalan sebelum app fully booted,
     * dan menghindari side-effect dari Model events.
     * Untuk seeder, DB facade lebih aman dan eksplisit.
     */
    public function run(): void
    {
        $now = now();

        // Hash "password" — sama seperti di SQL asli lo
        $hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

        DB::table('users')->insert([
            // ── Admin ────────────────────────────────────────
            [
                'username'      => 'admin',
                'password_hash' => $hash,
                'nama_lengkap'  => 'Administrator System',
                'email'         => 'admin@cafe.id',
                'telepon'       => '081111111111',
                'role'          => 'admin',
                'api_key'       => Str::random(64),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // ── Kasir ─────────────────────────────────────────
            [
                'username'      => 'kasir',
                'password_hash' => $hash,
                'nama_lengkap'  => 'Kasir Cafe',
                'email'         => 'kasir@cafe.id',
                'telepon'       => '082222222222',
                'role'          => 'kasir',
                'api_key'       => Str::random(64),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // ── Customer 1 ────────────────────────────────────
            [
                'username'      => 'customer1',
                'password_hash' => $hash,
                'nama_lengkap'  => 'Customer Satu',
                'email'         => 'cust1@email.com',
                'telepon'       => '083333333333',
                'role'          => 'customer',
                'api_key'       => Str::random(64),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // ── Customer 2 ────────────────────────────────────
            [
                'username'      => 'customer2',
                'password_hash' => $hash,
                'nama_lengkap'  => 'Customer Dua',
                'email'         => 'cust2@email.com',
                'telepon'       => '084444444444',
                'role'          => 'customer',
                'api_key'       => Str::random(64),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ]);
    }
}