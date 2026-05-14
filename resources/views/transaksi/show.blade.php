@extends('layouts.app')
@section('title','Detail Transaksi')
@section('page-icon','fa-receipt')
@section('page-title','Detail Transaksi')
@section('header-actions')
    <button onclick="window.print()" class="action-btn action-btn-primary"><i class="fas fa-print"></i> Cetak Struk</button>
    <a href="{{ route('transaksi.index') }}" class="action-btn action-btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="grid-card" style="max-width:700px; margin:0 auto;">
    <div style="text-align:center; margin-bottom:30px;">
        <h2 style="color:var(--gold-main); font-size:24px;">🧾 TRX-{{ str_pad($transaksi->id_transaksi,4,'0',STR_PAD_LEFT) }}</h2>
        <p style="color:var(--neutral-light);">{{ $transaksi->created_at->format('d F Y, H:i') }}</p>
        @if($transaksi->eco_packaging)
        <span style="background:rgba(46,125,50,0.1); color:var(--success); padding:4px 14px; border-radius:20px; font-size:13px; font-weight:600;">🌿 Eco Packaging</span>
        @endif
    </div>

    <div style="background:rgba(212,175,55,0.05); border-radius:12px; padding:20px; margin-bottom:25px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
            <span style="color:var(--neutral-light);">Customer</span>
            <strong>{{ $transaksi->nama_customer }}</strong>
        </div>
        @if($transaksi->no_meja)
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
            <span style="color:var(--neutral-light);">No. Meja</span>
            <strong>{{ $transaksi->no_meja }}</strong>
        </div>
        @endif
        @if($transaksi->catatan)
        <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--neutral-light);">Catatan</span>
            <strong>{{ $transaksi->catatan }}</strong>
        </div>
        @endif
    </div>

    <table class="table" style="margin-bottom:25px;">
        <thead><tr><th>Menu</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
        <tbody>
        @foreach($transaksi->details as $d)
        <tr>
            <td>{{ $d->menu->nama_menu }}</td>
            <td>{{ $d->qty }}x</td>
            <td>Rp {{ number_format($d->harga_satuan,0,',','.') }}</td>
            <td><strong>Rp {{ number_format($d->subtotal,0,',','.') }}</strong></td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div style="background:rgba(212,175,55,0.05); border-radius:12px; padding:20px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <span>Subtotal</span><span>Rp {{ number_format($transaksi->total,0,',','.') }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
            <span>Pajak (3%)</span><span>Rp {{ number_format($transaksi->pajak,0,',','.') }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:22px; font-weight:800; color:var(--gold-main); border-top:2px solid rgba(212,175,55,0.2); padding-top:15px;">
            <span>TOTAL</span><span>Rp {{ number_format($transaksi->grand_total,0,',','.') }}</span>
        </div>
    </div>

    @if(auth()->user()->isKasir())
    <div style="margin-top:25px; text-align:center;">
        <a href="{{ route('transaksi.create') }}" class="action-btn action-btn-primary" style="padding:15px 40px; font-size:16px;">
            <i class="fas fa-cash-register"></i> Transaksi Baru
        </a>
    </div>
    @endif
</div>

<style>
@media print {
    .sidebar, .top-header, .header-right, .mobile-menu-toggle { display:none!important; }
    .main-content { margin-left:0!important; }
    .grid-card { box-shadow:none!important; border:1px solid #ddd!important; }
}
</style>
@endsection