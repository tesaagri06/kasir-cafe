<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Semua setting key dari kasir_cafe.sql asli.
     *
     * Tambahan baru: sdgs_eco_packaging_enabled dan sdgs_waste_report_enabled
     * Ini toggle untuk fitur SDGS — bisa di-on/off dari pengaturan.
     *
     * Pattern insert ini pakai updateOrInsert bukan insert biasa.
     * Kenapa? Kalau seeder dijalankan ulang (php artisan db:seed),
     * pakai insert biasa akan error duplicate key.
     * updateOrInsert: kalau key sudah ada → update value-nya,
     * kalau belum ada → insert baru. Aman dijalankan berkali-kali.
     */
    public function run(): void
    {
        $settings = [
            // ── Informasi Cafe ────────────────────────────────
            'nama_cafe'      => 'CafePOS System',
            'alamat_cafe'    => 'Jl. Contoh No. 123, Kota Surabaya',
            'telepon_cafe'   => '(031) 123-4567',

            // ── Konfigurasi Transaksi ─────────────────────────
            'pajak'          => '3',     // persen, bukan desimal
            'currency'       => 'IDR',
            'timezone'       => 'Asia/Jakarta',

            // ── Struk ─────────────────────────────────────────
            'receipt_header' => 'Terima kasih telah berkunjung!',
            'receipt_footer' => 'Struk ini sebagai bukti pembayaran yang sah',

            // ── SDGS Settings (baru) ──────────────────────────
            // Toggle fitur eco-packaging di halaman transaksi
            'sdgs_eco_packaging_enabled' => 'true',

            // Toggle laporan food waste di dashboard
            'sdgs_waste_report_enabled'  => 'true',

            // Pesan eco-packaging yang tampil ke customer
            'sdgs_eco_message' => 'Pilih eco-packaging untuk mengurangi sampah plastik!',
        ];

        $now = now();

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['setting_key' => $key],          // kondisi pencarian
                [                                  // data yang di-set
                    'setting_value' => $value,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]
            );
        }
    }
}