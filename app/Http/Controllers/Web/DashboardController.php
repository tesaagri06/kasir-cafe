<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMenu       = Menu::where('status_menu', 'aktif')->count();
        $totalKategori   = Kategori::where('status', 'aktif')->count();
        $totalTransaksi  = Transaksi::whereDate('created_at', today())->count();
        $pendapatanHari  = Transaksi::where('status', 'selesai')
                                    ->whereDate('created_at', today())
                                    ->sum('grand_total');
        $stokMenipis     = Menu::where('stok', '<=', 5)
                               ->where('stok', '>', 0)
                               ->where('status_menu', 'aktif')
                               ->count();
        $ecoCount        = Transaksi::where('eco_packaging', true)
                                    ->whereMonth('created_at', now()->month)
                                    ->count();

        $transaksiTerbaru = Transaksi::with('details.menu')
                                     ->orderBy('created_at', 'desc')
                                     ->limit(5)
                                     ->get();

        $bestSelling = DB::table('detail_transaksi as dt')
            ->join('menu as m', 'dt.id_menu', '=', 'm.id_menu')
            ->selectRaw('m.nama_menu, SUM(dt.qty) as total_terjual')
            ->whereMonth('dt.created_at', now()->month)
            ->groupBy('m.id_menu', 'm.nama_menu')
            ->orderByRaw('SUM(dt.qty) DESC')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalMenu', 'totalKategori', 'totalTransaksi',
            'pendapatanHari', 'stokMenipis', 'ecoCount',
            'transaksiTerbaru', 'bestSelling'
        ));
    }
}