<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * 10 menu persis dari kasir_cafe.sql asli lo.
     *
     * Kenapa pakai id_kategori hardcode (1, 2, 3)?
     * Karena KategoriSeeder dijalankan duluan di DatabaseSeeder,
     * jadi urutan ID-nya pasti 1=Coffee, 2=NonCoffee, 3=Snack.
     * Kalau mau lebih aman bisa pakai DB::table('kategori')
     * ->where('nama_kategori', 'Coffee')->value('id_kategori'),
     * tapi untuk 10 item ini overkill.
     */
    public function run(): void
    {
        $now = now();

        $menus = [
            // Coffee (id_kategori = 1)
            ['nama_menu' => 'Espresso',          'harga' => 15000, 'stok' => 21, 'id_kategori' => 1],
            ['nama_menu' => 'Caffe Latte',        'harga' => 20000, 'stok' => 14, 'id_kategori' => 1],
            ['nama_menu' => 'Americano',          'harga' => 18000, 'stok' => 12, 'id_kategori' => 1],
            ['nama_menu' => 'Caramel Macchiato',  'harga' => 22000, 'stok' => 13, 'id_kategori' => 1],

            // Non Coffee (id_kategori = 2)
            ['nama_menu' => 'Matcha Latte',       'harga' => 19000, 'stok' => 12, 'id_kategori' => 2],
            ['nama_menu' => 'Oreo Shake',         'harga' => 17000, 'stok' => 14, 'id_kategori' => 2],
            ['nama_menu' => 'Chai Tea',           'harga' => 12000, 'stok' => 20, 'id_kategori' => 2],
            ['nama_menu' => 'Black Tea',          'harga' => 10000, 'stok' => 25, 'id_kategori' => 2],

            // Snack (id_kategori = 3)
            ['nama_menu' => 'Cheese Cake',        'harga' => 25000, 'stok' => 28, 'id_kategori' => 3],
            ['nama_menu' => 'MilkBun',            'harga' => 38000, 'stok' => 20, 'id_kategori' => 3],
        ];

        foreach ($menus as $menu) {
            DB::table('menu')->insert([
                'nama_menu'   => $menu['nama_menu'],
                'harga'       => $menu['harga'],
                'stok'        => $menu['stok'],
                'id_kategori' => $menu['id_kategori'],
                'status_menu' => 'aktif',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}