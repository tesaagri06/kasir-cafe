@extends('layouts.app')
@section('title','Riwayat Transaksi')
@section('page-icon','fa-history')
@section('page-title','Riwayat Transaksi')
@section('header-actions')
    @if(auth()->user()->isKasir() || auth()->user()->isAdmin())
    <a href="{{ route('transaksi.create') }}" class="action-btn action-btn-primary"><i class="fas fa-plus"></i> Transaksi Baru</a>
    @endif
@endsection

@section('content')
<div class="grid-card">
    <form method="GET" style="display:flex; gap:10px; margin-bottom:20px;">
        <input type="text" name="search" class="form-control" placeholder="Cari customer..." value="{{ request('search') }}" style="max-width:250px;">
        <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}" style="max-width:180px;">
        <button type="submit" class="action-btn action-btn-primary"><i class="fas fa-search"></i></button>
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Kode</th><th>Customer</th><th>Meja</th><th>Total</th><th>Eco</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($transaksis as $t)
            <tr>
                <td><strong style="color:var(--gold-main)">TRX-{{ str_pad($t->id_transaksi,4,'0',STR_PAD_LEFT) }}</strong></td>
                <td>{{ $t->nama_customer }}</td>
                <td>{{ $t->no_meja ?? '-' }}</td>
                <td>Rp {{ number_format($t->grand_total,0,',','.') }}</td>
                <td>{{ $t->eco_packaging ? '🌿 Ya' : '-' }}</td>
                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('transaksi.show',$t->id_transaksi) }}" class="action-btn action-btn-secondary" style="padding:6px 12px; font-size:12px;">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; padding:40px; color:var(--neutral-light);">Belum ada transaksi</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px;">{{ $transaksis->links() }}</div>
</div>
@endsection