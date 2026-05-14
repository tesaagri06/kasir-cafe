<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel kategori — tidak banyak berubah dari SQL asli lo.
     *
     * Satu hal yang ditambah: index pada kolom status.
     * Kenapa? Karena query paling sering di app ini adalah
     * "ambil semua kategori yang aktif" → WHERE status = 'aktif'
     * Index bikin query itu jauh lebih cepat di data banyak.
     */
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id('id_kategori');

            $table->string('nama_kategori', 50);
            $table->text('deskripsi')->nullable();

            // Icon name dari Font Awesome, contoh: 'coffee', 'glass-whiskey'
            $table->string('icon', 50)->default('tag');

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');

            $table->timestamps();

            // Index untuk query filter status yang sering dipakai
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};