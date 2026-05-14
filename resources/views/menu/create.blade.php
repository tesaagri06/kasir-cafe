@extends('layouts.app')
@section('title','Tambah Menu')
@section('page-icon','fa-plus-circle')
@section('page-title','Tambah Menu Baru')
@section('header-actions')
    <a href="{{ route('menu.index') }}" class="action-btn action-btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="grid-card" style="max-width:700px; margin:0 auto;">
    <form method="POST" action="{{ route('menu.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama Menu</label>
            <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu') }}" required>
            @error('nama_menu')<p style="color:var(--error); font-size:12px; margin-top:5px;">{{ $message }}</p>@enderror
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Stok Awal</label>
                <input type="number" name="stok" class="form-control" value="{{ old('stok',0) }}" min="0" required>
            </div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="id_kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($kategoris as $k)
                    <option value="{{ $k->id_kategori }}" {{ old('id_kategori')==$k->id_kategori?'selected':'' }}>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status_menu" class="form-control">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
        </div>
        <div style="display:flex; gap:15px; margin-top:20px;">
            <button type="submit" class="action-btn action-btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('menu.index') }}" class="action-btn action-btn-secondary"><i class="fas fa-times"></i> Batal</a>
        </div>
    </form>
</div>
@endsection