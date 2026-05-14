<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel detail_transaksi — ada satu tambahan krusial: harga_satuan.
     *
     * MASALAH DI PHP ASLI LO:
     * Di cetak_struk.php, lo hitung harga satuan dengan cara:
     *   $harga_satuan = $d['subtotal'] / $d['qty']
     *
     * Ini BUG TERSEMBUNYI. Skenario:
     * → Customer beli Espresso hari ini, harga Rp 15.000
     * → Besok kasir update harga Espresso jadi Rp 20.000
     * → Customer buka struk lama → harga masih tampil benar (15.000)
     *   karena dihitung dari subtotal yang sudah tersimpan
     *
     * TAPI bagaimana kalau ada pembatalan dan refund?
     * Atau audit laporan per item? Harga satuan aslinya harus ada.
     *
     * SOLUSI: simpan harga_satuan saat transaksi terjadi.
     * Kolom ini TIDAK berubah meski harga menu di-update nanti.
     *
     * ON DELETE rules:
     * - id_transaksi CASCADE  → detail ikut terhapus kalau transaksi dihapus (wajar)
     * - id_menu    RESTRICT   → TIDAK BISA hapus menu yang pernah ada di transaksi
     *                           Kalau mau "hapus" menu, gunakan status_menu = 'nonaktif'
     */
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');

            $table->foreignId('id_transaksi')
                  ->constrained('transaksi', 'id_transaksi')
                  ->cascadeOnDelete();  // hapus detail kalau transaksi dihapus

            $table->foreignId('id_menu')
                  ->constrained('menu', 'id_menu')
                  ->restrictOnDelete(); // BLOCK hapus menu yang ada di histori

            $table->integer('qty');

            // Harga satuan SAAT TRANSAKSI — tidak ikut berubah
            // walau harga menu di-update di kemudian hari
            $table->integer('harga_satuan');

            // subtotal = qty * harga_satuan (disimpan untuk performa)
            // Tidak perlu hitung ulang setiap kali query
            $table->integer('subtotal');

            $table->timestamps();

            // Index untuk query detail per transaksi (paling sering)
            $table->index('id_transaksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};