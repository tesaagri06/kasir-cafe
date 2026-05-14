<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel transaksi — beberapa perubahan signifikan dari SQL asli.
     *
     * PERUBAHAN 1: kolom 'tanggal' → dihapus, diganti created_at
     * Di SQL asli ada kolom 'tanggal' terpisah. Di Laravel, timestamps()
     * sudah generate created_at yang fungsinya sama persis.
     * Dua kolom dengan fungsi sama = redundant.
     *
     * PERUBAHAN 2: eco_packaging (SDGS baru)
     * Boolean flag apakah customer request eco-friendly packaging.
     * Ini untuk angle SDGs Lingkungan di laporan sustainability.
     *
     * PERUBAHAN 3: nullOnDelete untuk customer_id
     * Kalau data customer dihapus, transaksinya TETAP ADA.
     * Histori keuangan tidak boleh ikut terhapus — ini penting
     * dari sisi audit dan laporan bisnis.
     *
     * Index:
     * - created_at → untuk filter laporan per periode
     * - status     → untuk filter transaksi per status
     * - customer_id → untuk riwayat transaksi per customer
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');

            // Nullable: walk-in customer tidak perlu punya akun
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('users', 'id_user')
                  ->nullOnDelete()    // histori tetap ada walau user dihapus
                  ->cascadeOnUpdate();

            $table->string('nama_customer', 150)
                  ->default('Customer Walk-in');

            // No meja nullable — bisa takeaway tanpa nomor meja
            $table->unsignedTinyInteger('no_meja')->nullable();

            // Semua nilai dalam Rupiah (integer, tanpa desimal)
            $table->integer('total');       // subtotal sebelum pajak
            $table->integer('pajak');       // 3% dari total
            $table->integer('grand_total'); // total + pajak

            $table->enum('status', [
                'pending',
                'diproses',
                'selesai',
                'dibatalkan'
            ])->default('selesai');

            $table->text('catatan')->nullable();

            // SDGS: tracking eco-packaging request
            // Default false — customer harus opt-in
            $table->boolean('eco_packaging')->default(false);

            // created_at = tanggal transaksi (menggantikan kolom 'tanggal')
            $table->timestamps();

            // Index untuk query laporan dan filter
            $table->index('created_at');
            $table->index('status');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};