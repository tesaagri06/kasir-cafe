<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    // GET /api/transaksi
    public function index(Request $request): JsonResponse
    {
        $user    = auth('api')->user();
        $perPage = min((int) $request->get('per_page', 10), 50);

        $query = Transaksi::with(['customer', 'details.menu'])
                          ->orderBy('created_at', 'desc');

        // Customer hanya lihat transaksi miliknya
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id_user);
        }

        if ($search = $request->get('search')) {
            $query->where('nama_customer', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($dari = $request->get('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $dari);
        }

        if ($sampai = $request->get('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $sampai);
        }

        if ($request->has('eco_packaging')) {
            $query->where('eco_packaging', $request->boolean('eco_packaging'));
        }

        $transaksi = $query->paginate($perPage);
        $transaksi->appends($request->query());

        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi berhasil diambil.',
            'data'    => $transaksi->items(),
            'meta'    => [
                'current_page' => $transaksi->currentPage(),
                'last_page'    => $transaksi->lastPage(),
                'per_page'     => $transaksi->perPage(),
                'total'        => $transaksi->total(),
            ],
        ]);
    }

    // GET /api/transaksi/{id}
    public function show(Transaksi $transaksi): JsonResponse
    {
        $user = auth('api')->user();

        if ($user->isCustomer() && $transaksi->customer_id !== $user->id_user) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.',
            ], 403);
        }

        $transaksi->load(['details.menu.kategori', 'customer']);

        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi berhasil diambil.',
            'data'    => $transaksi,
        ]);
    }

    // POST /api/transaksi
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama_customer'   => 'nullable|string|max:150',
            'no_meja'         => 'nullable|integer|min:1|max:99',
            'catatan'         => 'nullable|string|max:500',
            'eco_packaging'   => 'nullable|boolean',
            'items'           => 'required|array|min:1',
            'items.*.id_menu' => 'required|integer|exists:menu,id_menu',
            'items.*.qty'     => 'required|integer|min:1|max:99',
        ]);

        $user = auth('api')->user();
        $customerId = $user->isCustomer() ? $user->id_user : null;

        try {
            $transaksi = DB::transaction(function () use ($request, $customerId) {

                // Load semua menu sekaligus — anti N+1
                $menuIds = collect($request->items)->pluck('id_menu')->unique();
                $menus   = Menu::whereIn('id_menu', $menuIds)
                               ->lockForUpdate()
                               ->get()
                               ->keyBy('id_menu');

                // Validasi stok semua item dulu sebelum insert apapun
                foreach ($request->items as $item) {
                    $menu = $menus->get($item['id_menu']);

                    if (!$menu) {
                        throw new \Exception("Menu ID {$item['id_menu']} tidak ditemukan.");
                    }
                    if ($menu->status_menu !== 'aktif') {
                        throw new \Exception("Menu '{$menu->nama_menu}' tidak aktif.");
                    }
                    if ($menu->stok < $item['qty']) {
                        throw new \Exception(
                            "Stok '{$menu->nama_menu}' tidak cukup. " .
                            "Tersedia: {$menu->stok}, Dibutuhkan: {$item['qty']}."
                        );
                    }
                }

                // Hitung subtotal
                $subtotal   = 0;
                $detailData = [];

                foreach ($request->items as $item) {
                    $menu         = $menus->get($item['id_menu']);
                    $itemSubtotal = $menu->harga * $item['qty'];
                    $subtotal    += $itemSubtotal;

                    $detailData[] = [
                        'id_menu'      => $item['id_menu'],
                        'qty'          => $item['qty'],
                        'harga_satuan' => $menu->harga,
                        'subtotal'     => $itemSubtotal,
                    ];
                }

                $pajak      = (int) round($subtotal * 0.03);
                $grandTotal = $subtotal + $pajak;

                // Insert transaksi
                $transaksi = Transaksi::create([
                    'customer_id'   => $customerId,
                    'nama_customer' => $request->nama_customer ?? 'Customer Walk-in',
                    'no_meja'       => $request->no_meja,
                    'catatan'       => $request->catatan,
                    'eco_packaging' => $request->eco_packaging ?? false,
                    'total'         => $subtotal,
                    'pajak'         => $pajak,
                    'grand_total'   => $grandTotal,
                    'status'        => 'selesai',
                ]);

                // Batch insert detail — 1 query untuk semua item
                $now = now();
                DetailTransaksi::insert(
                    array_map(fn($d) => array_merge($d, [
                        'id_transaksi' => $transaksi->id_transaksi,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]), $detailData)
                );

                // Kurangi stok setiap menu
                foreach ($request->items as $item) {
                    Menu::where('id_menu', $item['id_menu'])
                        ->decrement('stok', $item['qty']);
                }

                $transaksi->load(['details.menu.kategori', 'customer']);
                return $transaksi;
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat.',
                'data'    => [
                    'id'              => $transaksi->id_transaksi,
                    'kode'            => 'TRX-' . now()->format('Ymd') . '-' . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT),
                    'nama_customer'   => $transaksi->nama_customer,
                    'no_meja'         => $transaksi->no_meja,
                    'eco_packaging'   => $transaksi->eco_packaging,
                    'items'           => $transaksi->details->map(fn($d) => [
                        'nama_menu'    => $d->menu->nama_menu,
                        'qty'          => $d->qty,
                        'harga_satuan' => $d->harga_satuan,
                        'subtotal'     => $d->subtotal,
                    ]),
                    'summary' => [
                        'subtotal'             => $transaksi->total,
                        'pajak'                => $transaksi->pajak,
                        'grand_total'          => $transaksi->grand_total,
                        'grand_total_formatted'=> 'Rp ' . number_format($transaksi->grand_total, 0, ',', '.'),
                    ],
                    'tanggal' => $transaksi->created_at->format('Y-m-d H:i:s'),
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Transaksi gagal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }
}