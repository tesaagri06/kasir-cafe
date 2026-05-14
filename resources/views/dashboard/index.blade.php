@extends('layouts.app')
@section('title','Dashboard')
@section('page-icon','fa-tachometer-alt')
@section('page-title','Dashboard')

@section('content')

{{-- ============================================================
     STAT CARDS — 6 cards, auto-fit grid
     ============================================================ --}}
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-utensils"></i></div>
        <div class="stat-info">
            <h3>{{ $totalMenu }}</h3>
            <p>Menu Aktif</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-list-alt"></i></div>
        <div class="stat-info">
            <h3>{{ $totalKategori }}</h3>
            <p>Kategori</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-receipt"></i></div>
        <div class="stat-info">
            <h3>{{ $totalTransaksi }}</h3>
            <p>Transaksi Hari Ini</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
            <h3>Rp {{ number_format($pendapatanHari, 0, ',', '.') }}</h3>
            <p>Pendapatan Hari Ini</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info">
            <h3>{{ $stokMenipis }}</h3>
            <p>Stok Menipis</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-leaf"></i></div>
        <div class="stat-info">
            <h3>{{ $ecoCount }}</h3>
            <p>Eco Packaging Bulan Ini</p>
        </div>
    </div>

</div>

{{-- ============================================================
     MAIN GRID — Transaksi Terbaru + Best Selling
     ============================================================ --}}
<div class="two-col-grid">

    {{-- Transaksi Terbaru --}}
    <div class="grid-card">
        <div class="grid-card-header">
            <div class="grid-card-title">
                <i class="fas fa-receipt"></i>
                <h3>Transaksi Terbaru</h3>
            </div>
            <a href="{{ route('transaksi.index') }}" class="action-btn action-btn-secondary btn-sm">
                Lihat Semua
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Eco</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiTerbaru as $t)
                    <tr>
                        <td>
                            <a href="{{ route('transaksi.show', $t->id_transaksi) }}" class="trx-link">
                                TRX-{{ str_pad($t->id_transaksi, 4, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                        <td>{{ $t->nama_customer ?? 'Walk-in' }}</td>
                        <td class="font-medium">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td>
                        <td>
                            @if($t->eco_packaging)
                                <span class="badge badge-success">🌿 Eco</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="table-empty">Belum ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Menu Terlaris --}}
    <div class="grid-card">
        <div class="grid-card-header">
            <div class="grid-card-title">
                <i class="fas fa-star"></i>
                <h3>Menu Terlaris Bulan Ini</h3>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Menu</th>
                        <th>Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bestSelling as $i => $item)
                    <tr>
                        <td><span class="badge badge-primary">{{ $i + 1 }}</span></td>
                        <td>{{ $item->nama_menu }}</td>
                        <td><strong>{{ $item->total_terjual }}</strong> porsi</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="table-empty">Belum ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection