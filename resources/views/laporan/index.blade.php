@extends('layouts.app')
@section('title','Laporan')
@section('page-icon','fa-chart-pie')
@section('page-title','Laporan & Sustainability')

@section('content')

{{-- ============================================================
     FILTER TANGGAL
     ============================================================ --}}
<div class="grid-card section-gap">
    <form method="GET" class="filter-form">
        <div class="form-group mb-0">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ $dari }}">
        </div>
        <div class="form-group mb-0">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
        </div>
        <button type="submit" class="action-btn action-btn-primary" style="margin-top:auto;">
            <i class="fas fa-filter"></i> Filter
        </button>
        <a href="{{ route('laporan.index') }}" class="action-btn action-btn-secondary" style="margin-top:auto;">
            <i class="fas fa-undo"></i> Reset
        </a>
    </form>
</div>

{{-- ============================================================
     OVERVIEW STATS
     ============================================================ --}}
<div class="stats-grid section-gap">

    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-receipt"></i></div>
        <div class="stat-info">
            <h3>{{ $overview->total_transaksi }}</h3>
            <p>Total Transaksi</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
            <h3>Rp {{ number_format($overview->total_pendapatan, 0, ',', '.') }}</h3>
            <p>Total Pendapatan</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-leaf"></i></div>
        <div class="stat-info">
            <h3>{{ $ecoCount }} <small class="stat-percent">({{ $ecoPercent }}%)</small></h3>
            <p>Eco Packaging</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-recycle"></i></div>
        <div class="stat-info">
            <h3>{{ $plastikKg }} kg</h3>
            <p>Plastik Dikurangi</p>
        </div>
    </div>

</div>

{{-- ============================================================
     TABEL: Menu Terlaris + Pendapatan Harian
     ============================================================ --}}
<div class="two-col-grid section-gap">

    {{-- Menu Terlaris --}}
    <div class="grid-card">
        <div class="grid-card-header">
            <div class="grid-card-title">
                <i class="fas fa-star"></i>
                <h3>Menu Terlaris</h3>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Menu</th>
                        <th>Terjual</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bestSelling as $i => $item)
                    <tr>
                        <td><span class="badge badge-primary">{{ $i + 1 }}</span></td>
                        <td>{{ $item->nama_menu }}</td>
                        <td><strong>{{ $item->total_terjual }}</strong></td>
                        <td class="font-medium">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="table-empty">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pendapatan Harian --}}
    <div class="grid-card">
        <div class="grid-card-header">
            <div class="grid-card-title">
                <i class="fas fa-calendar-alt"></i>
                <h3>Pendapatan Harian</h3>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Transaksi</th>
                        <th>Pendapatan</th>
                        <th>Eco</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($harian as $h)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($h->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $h->total_transaksi }}</td>
                        <td class="font-medium">Rp {{ number_format($h->total_pendapatan, 0, ',', '.') }}</td>
                        <td>{{ $h->eco_count ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="table-empty">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ============================================================
     SDG SUSTAINABILITY REPORT
     ============================================================ --}}
<div class="grid-card sdg-wrapper section-gap">

    <div class="grid-card-header">
        <div class="grid-card-title">
            <i class="fas fa-leaf sdg-title-icon"></i>
            <h3 class="sdg-title-text">Sustainability Report — SDGs</h3>
        </div>
    </div>

    <div class="sdg-grid">

        {{-- SDG 12 --}}
        <div class="sdg-card sdg-green">
            <div class="sdg-emoji">♻️</div>
            <h4 class="sdg-label">SDG 12</h4>
            <p class="sdg-desc">Responsible Consumption</p>
            <div class="sdg-value">{{ $ecoPercent }}%</div>
            <p class="sdg-sub">Transaksi pakai eco-packaging</p>
        </div>

        {{-- SDG 13 --}}
        <div class="sdg-card sdg-blue">
            <div class="sdg-emoji">🌍</div>
            <h4 class="sdg-label">SDG 13</h4>
            <p class="sdg-desc">Climate Action</p>
            <div class="sdg-value">{{ $plastikKg }} kg</div>
            <p class="sdg-sub">Plastik tidak jadi sampah</p>
        </div>

        {{-- SDG 8 --}}
        <div class="sdg-card sdg-gold">
            <div class="sdg-emoji">💰</div>
            <h4 class="sdg-label">SDG 8</h4>
            <p class="sdg-desc">Economic Growth</p>
            <div class="sdg-value sdg-value-sm">
                Rp {{ number_format($overview->total_pendapatan, 0, ',', '.') }}
            </div>
            <p class="sdg-sub">Revenue periode ini</p>
        </div>

    </div>
</div>

@endsection