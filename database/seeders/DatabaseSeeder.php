<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Orchestrator — urutan pemanggilan seeder SANGAT penting.
     *
     * Urutan harus mengikuti dependency antar tabel:
     *
     * users         → tidak ada FK, bisa jalan pertama
     * kategori      → tidak ada FK, bisa jalan pertama
     * menu          → butuh kategori sudah ada (FK id_kategori)
     * settings      → tidak ada FK, bisa kapan saja
     *
     * Transaksi, detail_transaksi, food_waste_log TIDAK di-seed
     * karena itu adalah data real operasional, bukan data awal.
     *
     * Cara jalankan:
     *   php artisan migrate:fresh --seed
     *
     * Atau kalau mau seed ulang tanpa drop tabel:
     *   php artisan db:seed
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,      // 1. Users dulu — tidak ada dependency
            KategoriSeeder::class,  // 2. Kategori — tidak ada dependency
            MenuSeeder::class,      // 3. Menu — butuh kategori sudah ada
            SettingsSeeder::class,  // 4. Settings — tidak ada dependency
        ]);
    }
}