<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel menu — ada satu perubahan penting dari SQL asli.
     *
     * Di SQL asli, ON DELETE untuk id_kategori adalah SET NULL.
     * Kita pertahankan itu — menu tidak ikut terhapus kalau
     * kategorinya dihapus, cuma id_kategori-nya jadi NULL.
     * Itu keputusan bisnis yang masuk akal.
     *
     * Index tambahan:
     * - status_menu → sering di-filter "WHERE status_menu = 'aktif'"
     * - id_kategori → sering di-filter per kategori
     * - Compound (status_menu, stok) → untuk query menu tersedia
     */
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id('id_menu');

            $table->string('nama_menu', 150);
            $table->integer('harga');   // dalam Rupiah, tanpa desimal
            $table->integer('stok')->default(0);

            // nullOnDelete: kalau kategori dihapus, menu tetap ada
            // cascadeOnUpdate: kalau id kategori berubah, ikut update
            $table->foreignId('id_kategori')
                  ->nullable()
                  ->constrained('kategori', 'id_kategori')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->enum('status_menu', ['aktif', 'nonaktif'])
                  ->default('aktif');

            $table->timestamps();

            // Index untuk query yang paling sering
            $table->index('status_menu');
            $table->index(['status_menu', 'stok']); // menu tersedia = aktif DAN stok > 0
        });
    }

    public function down(): void
    {
        // Drop tabel menu dulu sebelum kategori karena ada FK
        Schema::dropIfExists('menu');
    }
};