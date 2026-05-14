<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Kategori;
use Illuminate\Http\Request;

class MenuWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('kategori');

        if ($search = $request->get('search')) {
            $query->where('nama_menu', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status_menu', $status);
        }

        $menus = $query->orderBy('nama_menu')->paginate(10);

        return view('menu.index', compact('menus'));
    }

    public function create()
    {
        $kategoris = Kategori::where('status', 'aktif')->get();

        return view('menu.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu'   => 'required|string|max:150|unique:menu,nama_menu',
            'harga'       => 'required|integer|min:1',
            'stok'        => 'required|integer|min:0',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'status_menu' => 'required|in:aktif,nonaktif',
        ]);

        Menu::create($request->only([
            'nama_menu',
            'harga',
            'stok',
            'id_kategori',
            'status_menu'
        ]));

        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        $kategoris = Kategori::where('status', 'aktif')->get();

        return view('menu.edit', compact('menu', 'kategoris'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama_menu'   => 'required|string|max:150|unique:menu,nama_menu,' . $menu->id_menu . ',id_menu',
            'harga'       => 'required|integer|min:1',
            'stok'        => 'required|integer|min:0',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'status_menu' => 'required|in:aktif,nonaktif',
        ]);

        $menu->update($request->only([
            'nama_menu',
            'harga',
            'stok',
            'id_kategori',
            'status_menu'
        ]));

        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil diperbarui!');
    }

    // Toggle status aktif / nonaktif
    public function toggleStatus(Menu $menu)
    {
        $menu->update([
            'status_menu' => $menu->status_menu === 'aktif'
                ? 'nonaktif'
                : 'aktif'
        ]);

        return back()->with(
            'success',
            "Menu '{$menu->nama_menu}' berhasil " .
            ($menu->status_menu === 'aktif'
                ? 'diaktifkan'
                : 'dinonaktifkan') . "."
        );
    }

    public function destroy(Menu $menu)
    {
        if ($menu->detailTransaksi()->exists()) {

            $menu->update([
                'status_menu' => 'nonaktif'
            ]);

            return redirect()->route('menu.index')
                ->with('success', 'Menu dinonaktifkan karena pernah ada di transaksi.');
        }

        $menu->delete();

        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil dihapus!');
    }
}