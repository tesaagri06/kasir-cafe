<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriWebController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('menu')->orderBy('nama_kategori')->paginate(10);
        return view('kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori,nama_kategori',
            'deskripsi'     => 'nullable|string|max:500',
            'icon'          => 'nullable|string|max:50',
            'status'        => 'required|in:aktif,nonaktif',
        ]);
        Kategori::create($request->only(['nama_kategori','deskripsi','icon','status']));
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori,nama_kategori,'.$kategori->id_kategori.',id_kategori',
            'deskripsi'     => 'nullable|string|max:500',
            'icon'          => 'nullable|string|max:50',
            'status'        => 'required|in:aktif,nonaktif',
        ]);
        $kategori->update($request->only(['nama_kategori','deskripsi','icon','status']));
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->menu()->where('status_menu','aktif')->count() > 0) {
            return redirect()->route('kategori.index')->with('error', 'Tidak bisa hapus — masih ada menu aktif!');
        }
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus!');
    }
}