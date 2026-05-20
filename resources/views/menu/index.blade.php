@extends('layouts.app')
@section('title','Kelola Menu')
@section('page-icon','fa-utensils')
@section('page-title','Kelola Menu')

@section('header-actions')
    <a href="{{ route('menu.create') }}" class="action-btn action-btn-primary">
        <i class="fas fa-plus"></i> Tambah Menu
    </a>
@endsection

@section('content')
<div class="grid-card">

    {{-- Search & Filter --}}
    <form method="GET" class="filter-form">
        <input
            type="text"
            name="search"
            class="form-control filter-search"
            placeholder="Cari nama menu..."
            value="{{ request('search') }}"
        >
        <select name="status" class="form-control filter-select">
            <option value="">Semua Status</option>
            <option value="aktif"    {{ request('status') == 'aktif'    ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="action-btn action-btn-primary btn-icon-only">
            <i class="fas fa-search"></i>
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('menu.index') }}" class="action-btn action-btn-secondary btn-icon-only" title="Reset filter">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $menu)
                <tr>
                    <td><strong>{{ $menu->nama_menu }}</strong></td>

                    <td>{{ $menu->kategori->nama_kategori ?? '—' }}</td>

                    <td class="font-medium">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>

                    <td>
                        @if($menu->stok == 0)
                            <span class="badge badge-danger">Habis</span>
                        @elseif($menu->stok <= 5)
                            <span class="badge badge-warning">{{ $menu->stok }} ⚠</span>
                        @else
                            <span class="badge badge-success">{{ $menu->stok }}</span>
                        @endif
                    </td>

                    <td>
                        <span class="badge {{ $menu->status_menu == 'aktif' ? 'badge-success' : 'badge-danger' }}">
                            {{ $menu->status_menu == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>

                    <td>
                        <div class="action-cell">
                            {{-- Edit --}}
                            <a href="{{ route('menu.edit', $menu->id_menu) }}"
                               class="action-icon action-icon-edit"
                               title="Edit menu">
                                <i class="fas fa-pen"></i>
                            </a>

                            {{-- Toggle status aktif/nonaktif --}}
                            <form method="POST" action="{{ route('menu.toggleStatus', $menu->id_menu) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="action-icon {{ $menu->status_menu == 'aktif' ? 'action-icon-warning' : 'action-icon-view' }}"
                                        title="{{ $menu->status_menu == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $menu->status_menu == 'aktif' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>

                            {{-- Delete --}}
                            <form method="POST"
                                  action="{{ route('menu.destroy', $menu->id_menu) }}"
                                  onsubmit="return confirm('Hapus menu \'{{ $menu->nama_menu }}\'? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="action-icon action-icon-delete"
                                        title="Hapus menu">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="table-empty">
                        <i class="fas fa-utensils" style="font-size:24px; opacity:0.3; display:block; margin-bottom:8px;"></i>
                        Belum ada menu. <a href="{{ route('menu.create') }}">Tambah sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($menus->hasPages())
    <div class="pagination-wrapper">
        {{ $menus->links() }}
    </div>
    @endif

</div>
@endsection
