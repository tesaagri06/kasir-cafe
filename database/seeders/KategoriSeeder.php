<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('kategori')->insert([
            [
                'nama_kategori' => 'Coffee',
                'deskripsi'     => 'Minuman berbasis espresso dan kopi segar',
                'icon'          => 'coffee',
                'status'        => 'aktif',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'nama_kategori' => 'Non Coffee',
                'deskripsi'     => 'Minuman tanpa kopi: teh, matcha, shake, dll',
                'icon'          => 'glass-whiskey',
                'status'        => 'aktif',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'nama_kategori' => 'Snack',
                'deskripsi'     => 'Camilan dan makanan ringan',
                'icon'          => 'cookie',
                'status'        => 'aktif',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ]);
    }
}