<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('ping', fn() => response()->json([
    'success' => true,
    'message' => 'CafePOS API is running.',
    'version' => '1.0.0',
]));

// ── Auth (public) ──────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout',  [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me',       [AuthController::class, 'me']);
    });
});

// ── Route pakai Basic Auth ─────────────────────────────────
Route::middleware('basic.auth')->prefix('basic')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
});

// ── Route pakai API Key ────────────────────────────────────
Route::middleware('api.key')->prefix('key')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::get('menu', [MenuController::class, 'index']);
});

// ── Protected — semua butuh JWT ────────────────────────────
Route::middleware('auth:api')->group(function () {

    // Kategori
    Route::get('kategori',             [KategoriController::class, 'index']);
    Route::get('kategori/{kategori}',  [KategoriController::class, 'show']);

    Route::middleware('role:kasir,admin')->group(function () {
        Route::post('kategori',              [KategoriController::class, 'store']);
        Route::patch('kategori/{kategori}',  [KategoriController::class, 'update']);
        Route::delete('kategori/{kategori}', [KategoriController::class, 'destroy']);
    });

    // Menu
    Route::get('menu',        [MenuController::class, 'index']);
    Route::get('menu/{menu}', [MenuController::class, 'show']);

    Route::middleware('role:kasir,admin')->group(function () {
        Route::post('menu',         [MenuController::class, 'store']);
        Route::patch('menu/{menu}', [MenuController::class, 'update']);
        Route::delete('menu/{menu}',[MenuController::class, 'destroy']);
    });

    // Transaksi
    Route::get('transaksi',             [TransaksiController::class, 'index']);
    Route::post('transaksi',            [TransaksiController::class, 'store']);
    Route::get('transaksi/{transaksi}', [TransaksiController::class, 'show']);

    // Laporan — kasir dan admin saja
    Route::middleware('role:kasir,admin')->prefix('laporan')->group(function () {
        Route::get('overview',      [ReportController::class, 'overview']);
        Route::get('harian',        [ReportController::class, 'harian']);
        Route::get('best-selling',  [ReportController::class, 'bestSelling']);
        Route::get('sustainability',[ReportController::class, 'sustainability']);
        Route::post('food-waste',   [ReportController::class, 'storeFoodWaste']);
    });
});