<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MenuWebController;
use App\Http\Controllers\Web\KategoriWebController;
use App\Http\Controllers\Web\TransaksiWebController;
use App\Http\Controllers\Web\LaporanWebController;
use Illuminate\Support\Facades\Route;

// ── Auth (public) ─────────────────────────────────────────
Route::get('/', [AuthWebController::class, 'showLogin'])->name('home');
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

// ── Protected ─────────────────────────────────────────────
Route::middleware('web.auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // PROFIL
    Route::get('/profil', [ProfilWebController::class, 'index'])->name('profil.index');
    Route::put('/profil', [ProfilWebController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilWebController::class, 'updatePassword'])->name('profil.password');

    // PENGATURAN (SUDAH CLEAN)
    Route::get('/pengaturan', [PengaturanWebController::class, 'index'])
        ->name('pengaturan.index');

    Route::put('/pengaturan', [PengaturanWebController::class, 'update'])
        ->name('pengaturan.update');

    // MENU
    Route::patch('/menu/{menu}/toggle-status', [MenuWebController::class, 'toggleStatus'])
        ->name('menu.toggleStatus');

    Route::resource('menu', MenuWebController::class);

    // KATEGORI
    Route::resource('kategori', KategoriWebController::class);

    // TRANSAKSI
    Route::get('/transaksi', [TransaksiWebController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/buat', [TransaksiWebController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [TransaksiWebController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{transaksi}', [TransaksiWebController::class, 'show'])->name('transaksi.show');

    // LAPORAN
    Route::get('/laporan', [LaporanWebController::class, 'index'])->name('laporan.index');
});