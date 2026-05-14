<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini WAJIB ada untuk Laravel Sanctum (JWT).
     *
     * Ini adalah tabel standar bawaan Sanctum — kita tidak
     * ubah strukturnya sama sekali. Sanctum menyimpan semua
     * token (Bearer token dari login) di sini.
     *
     * Flow JWT di project ini:
     * POST /api/auth/login  → dapat token → simpan di sini
     * POST /api/auth/logout → token di-revoke dari sini
     *
     * Kenapa Sanctum bukan jwt-auth (tymon)?
     * Sanctum adalah paket resmi Laravel, built-in di Laravel 11,
     * zero konfigurasi tambahan, dan sudah cover semua requirement
     * Basic Auth + API Key + token-based auth.
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();

            // Polymorphic — bisa dipakai oleh model apapun,
            // tapi di project ini hanya User yang pakai
            $table->morphs('tokenable');

            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();

            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};