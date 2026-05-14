@extends('layouts.app')
@section('title','Edit Kategori')
@section('page-icon','fa-edit')
@section('page-title','Edit Kategori')
@section('header-actions')
    <a href="{{ route('kategori.index') }}" class="action-btn action-btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="grid-card" style="max-width:600px; margin:0 auto;">
    <form method="POST" action="{{ route('kategori.update',$kategori->id_kategori) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori',$kategori->nama_kategori) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Icon</label>
            <input type="text" name="icon" class="form-control" value="{{ old('icon',$kategori->icon) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi',$kategori->deskripsi) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="aktif" {{ $kategori->status=='aktif'?'selected':'' }}>Aktif</option>
                <option value="nonaktif" {{ $kategori->status=='nonaktif'?'selected':'' }}>Nonaktif</option>
            </select>
        </div>
        <div style="display:flex; gap:15px; margin-top:20px;">
            <button type="submit" class="action-btn action-btn-primary"><i class="fas fa-save"></i> Update</button>
            <a href="{{ route('kategori.index') }}" class="action-btn action-btn-secondary"><i class="fas fa-times"></i> Batal</a>
        </div>
    </form>
</div>
@endsection