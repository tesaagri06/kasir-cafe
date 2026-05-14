@extends('layouts.app')
@section('title','Tambah Kategori')
@section('page-icon','fa-plus-circle')
@section('page-title','Tambah Kategori')
@section('header-actions')
    <a href="{{ route('kategori.index') }}" class="action-btn action-btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="grid-card" style="max-width:600px; margin:0 auto;">
    <form method="POST" action="{{ route('kategori.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori') }}" required>
            @error('nama_kategori')<p style="color:var(--error); font-size:12px;">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Icon (Font Awesome name)</label>
            <input type="text" name="icon" class="form-control" value="{{ old('icon','tag') }}" placeholder="coffee, utensils, cookie, dll">
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>
        <div style="display:flex; gap:15px; margin-top:20px;">
            <button type="submit" class="action-btn action-btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('kategori.index') }}" class="action-btn action-btn-secondary"><i class="fas fa-times"></i> Batal</a>
        </div>
    </form>
</div>
@endsection