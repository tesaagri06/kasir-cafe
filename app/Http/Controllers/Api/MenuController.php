<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // GET /api/menu
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);

        $query = Menu::with('kategori');

        if ($search = $request->get('search')) {
            $query->where('nama_menu', 'like', "%{$search}%");
        }

        if ($kategoriId = $request->get('kategori_id')) {
            $query->where('id_kategori', (int) $kategoriId);
        }

        if ($status = $request->get('status')) {
            $query->where('status_menu', $status);
        }

        if ($request->boolean('stok_habis')) {
            $query->where('stok', 0);
        }

        if ($request->boolean('stok_menipis')) {
            $query->whereBetween('stok', [1, 5]);
        }

        $menu = $query->orderBy('nama_menu')->paginate($perPage);
        $menu->appends($request->query());

        return response()->json([
            'success' => true,
            'message' => 'Daftar menu berhasil diambil.',
            'data'    => $menu->items(),
            'meta'    => [
                'current_page' => $menu->currentPage(),
                'last_page'    => $menu->lastPage(),
                'per_page'     => $menu->perPage(),
                'total'        => $menu->total(),
            ],
        ]);
    }

    // GET /api/menu/{id}
    public function show(Menu $menu): JsonResponse
    {
        $menu->load('kategori');

        return response()->json([
            'success' => true,
            'message' => 'Detail menu berhasil diambil.',
            'data'    => $menu,
        ]);
    }

    // POST /api/menu
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_menu'   => 'required|string|max:150|unique:menu,nama_menu',
            'harga'       => 'required|integer|min:1',
            'stok'        => 'required|integer|min:0',
            'id_kategori' => 'required|integer|exists:kategori,id_kategori',
            'status_menu' => 'nullable|in:aktif,nonaktif',
        ]);

        $menu = Menu::create([
            'nama_menu'   => $validated['nama_menu'],
            'harga'       => $validated['harga'],
            'stok'        => $validated['stok'],
            'id_kategori' => $validated['id_kategori'],
            'status_menu' => $validated['status_menu'] ?? 'aktif',
        ]);

        $menu->load('kategori');

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan.',
            'data'    => $menu,
        ], 201);
    }

    // PATCH /api/menu/{id}
    public function update(Request $request, Menu $menu): JsonResponse
    {
        $validated = $request->validate([
            'nama_menu'   => 'sometimes|string|max:150|unique:menu,nama_menu,' . $menu->id_menu . ',id_menu',
            'harga'       => 'sometimes|integer|min:1',
            'stok'        => 'sometimes|integer|min:0',
            'id_kategori' => 'sometimes|integer|exists:kategori,id_kategori',
            'status_menu' => 'sometimes|in:aktif,nonaktif',
        ]);

        $menu->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diperbarui.',
            'data'    => $menu->fresh()->load('kategori'),
        ]);
    }

    // DELETE /api/menu/{id}
    public function destroy(Menu $menu): JsonResponse
    {
        if ($menu->detailTransaksi()->exists()) {
            $menu->update(['status_menu' => 'nonaktif']);
            return response()->json([
                'success' => true,
                'message' => 'Menu tidak bisa dihapus karena pernah ada di transaksi. Menu dinonaktifkan.',
                'data'    => $menu->fresh(),
            ]);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus.',
            'data'    => null,
        ]);
    }
}