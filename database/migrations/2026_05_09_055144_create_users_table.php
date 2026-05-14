<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel users — pondasi autentikasi seluruh sistem.
     *
     * Kenapa api_key ada di sini dan bukan di tabel terpisah?
     * Karena requirement UAS minta API Key per-user, satu user
     * satu key. Kalau relasi 1:N baru butuh tabel terpisah.
     *
     * Kenapa password_hash bukan password?
     * Kolom asli lo namanya password_hash, kita pertahankan.
     * Tapi nanti di Model kita override getAuthPassword()
     * supaya Laravel tau kolom mana yang dipakai auth.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');

            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->string('nama_lengkap', 100);

            // Dua field ini ada di SQL lo tapi tidak diproses
            // di register.php — sekarang disimpan dengan benar
            $table->string('email', 150)->nullable()->unique();
            $table->string('telepon', 20)->nullable();

            $table->enum('role', ['admin', 'kasir', 'customer'])
                  ->default('customer');

            // API Key untuk autentikasi berbasis header X-API-KEY
            // Di-generate saat user dibuat, bisa di-regenerate
            $table->string('api_key', 64)->nullable()->unique();

            $table->timestamps(); // created_at + updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};