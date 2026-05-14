<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // GET /api/kategori
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);

        $query = Kategori::query();

        if ($search = $request->get('search')) {
            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($request->boolean('with_menu')) {
            $query->with('menu');
        }

        $kategori = $query->orderBy('nama_kategori')->paginate($perPage);
        $kategori->appends($request->query());

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori berhasil diambil.',
            'data'    => $kategori->items(),
            'meta'    => [
                'current_page' => $kategori->currentPage(),
                'last_page'    => $kategori->lastPage(),
                'per_page'     => $kategori->perPage(),
                'total'        => $kategori->total(),
            ],
        ]);
    }

    // GET /api/kategori/{id}
    public function show(Kategori $kategori): JsonResponse
    {
        $kategori->load('menu');

        return response()->json([
            'success' => true,
            'message' => 'Detail kategori berhasil diambil.',
            'data'    => $kategori,
        ]);
    }

    // POST /api/kategori
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori,nama_kategori',
            'deskripsi'     => 'nullable|string|max:500',
            'icon'          => 'nullable|string|max:50',
            'status'        => 'nullable|in:aktif,nonaktif',
        ]);

        $kategori = Kategori::create([
            'nama_kategori' => $validated['nama_kategori'],
            'deskripsi'     => $validated['deskripsi'] ?? null,
            'icon'          => $validated['icon'] ?? 'tag',
            'status'        => $validated['status'] ?? 'aktif',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data'    => $kategori,
        ], 201);
    }

    // PATCH /api/kategori/{id}
    public function update(Request $request, Kategori $kategori): JsonResponse
    {
        $validated = $request->validate([
            'nama_kategori' => 'sometimes|string|max:50|unique:kategori,nama_kategori,' . $kategori->id_kategori . ',id_kategori',
            'deskripsi'     => 'nullable|string|max:500',
            'icon'          => 'nullable|string|max:50',
            'status'        => 'nullable|in:aktif,nonaktif',
        ]);

        $kategori->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data'    => $kategori->fresh(),
        ]);
    }

    // DELETE /api/kategori/{id}
    public function destroy(Kategori $kategori): JsonResponse
    {
        $jumlahMenu = $kategori->menu()->where('status_menu', 'aktif')->count();

        if ($jumlahMenu > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tidak bisa menghapus. Masih ada {$jumlahMenu} menu aktif di kategori ini.",
            ], 409);
        }

        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
            'data'    => null,
        ]);
    }
}