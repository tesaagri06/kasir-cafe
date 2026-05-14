<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiWebController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Transaksi::with('details.menu')->orderBy('created_at','desc');

        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id_user);
        }
        if ($search = $request->get('search')) {
            $query->where('nama_customer','like',"%{$search}%");
        }
        if ($tgl = $request->get('tanggal')) {
            $query->whereDate('created_at', $tgl);
        }

        $transaksis = $query->paginate(15);
        return view('transaksi.index', compact('transaksis'));
    }

    public function create()
    {
        $kategoris = Kategori::with('menuAktif')->where('status','aktif')->get();
        return view('transaksi.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'nullable|string|max:150',
            'no_meja'       => 'nullable|integer|min:1|max:99',
            'catatan'       => 'nullable|string|max:500',
            'eco_packaging' => 'nullable|boolean',
            'items'         => 'required|array|min:1',
            'items.*.id_menu' => 'required|exists:menu,id_menu',
            'items.*.qty'     => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($request, $user) {
                $menuIds = collect($request->items)->pluck('id_menu')->unique();
                $menus   = Menu::whereIn('id_menu', $menuIds)->lockForUpdate()->get()->keyBy('id_menu');

                foreach ($request->items as $item) {
                    $menu = $menus->get($item['id_menu']);
                    if ($menu->stok < $item['qty']) {
                        throw new \Exception("Stok '{$menu->nama_menu}' tidak cukup!");
                    }
                }

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

                $transaksi = Transaksi::create([
                    'customer_id'   => $user->isCustomer() ? $user->id_user : null,
                    'nama_customer' => $request->nama_customer ?? 'Customer Walk-in',
                    'no_meja'       => $request->no_meja,
                    'catatan'       => $request->catatan,
                    'eco_packaging' => $request->boolean('eco_packaging'),
                    'total'         => $subtotal,
                    'pajak'         => $pajak,
                    'grand_total'   => $grandTotal,
                    'status'        => 'selesai',
                ]);

                $now = now();
                DetailTransaksi::insert(array_map(fn($d) => array_merge($d, [
                    'id_transaksi' => $transaksi->id_transaksi,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]), $detailData));

                foreach ($request->items as $item) {
                    Menu::where('id_menu', $item['id_menu'])->decrement('stok', $item['qty']);
                }

                session(['last_transaksi_id' => $transaksi->id_transaksi]);
            });

            return redirect()->route('transaksi.show', session('last_transaksi_id'))
                             ->with('success', 'Transaksi berhasil!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Transaksi $transaksi)
    {
        $user = Auth::user();
        if ($user->isCustomer() && $transaksi->customer_id !== $user->id_user) {
            abort(403);
        }
        $transaksi->load(['details.menu.kategori', 'customer']);
        return view('transaksi.show', compact('transaksi'));
    }
}