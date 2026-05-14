@extends('layouts.app')
@section('title','Kategori')
@section('page-icon','fa-list-alt')
@section('page-title','Kelola Kategori')
@section('header-actions')
    <a href="{{ route('kategori.create') }}" class="action-btn action-btn-primary"><i class="fas fa-plus"></i> Tambah Kategori</a>
@endsection

@section('content')
<div class="grid-card">
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Nama Kategori</th><th>Icon</th><th>Jumlah Menu</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($kategoris as $k)
            <tr>
                <td><strong>{{ $k->nama_kategori }}</strong><br><small style="color:var(--neutral-light)">{{ $k->deskripsi }}</small></td>
                <td><i class="fas fa-{{ $k->icon ?? 'tag' }}"></i> {{ $k->icon }}</td>
                <td><span class="badge badge-primary">{{ $k->menu_count }} menu</span></td>
                <td><span class="badge {{ $k->status=='aktif'?'badge-success':'badge-danger' }}">{{ ucfirst($k->status) }}</span></td>
                <td style="display:flex; gap:8px;">
                    <a href="{{ route('kategori.edit',$k->id_kategori) }}" class="action-btn action-btn-secondary" style="padding:6px 12px; font-size:12px;"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{ route('kategori.destroy',$k->id_kategori) }}" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn action-btn-danger" style="padding:6px 12px; font-size:12px;"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; padding:40px;">Belum ada kategori</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px;">{{ $kategoris->links() }}</div>
</div>
@endsection