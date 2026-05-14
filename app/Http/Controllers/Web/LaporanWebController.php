<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanWebController extends Controller
{
    public function index(Request $request)
    {
        $dari   = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $overview = DB::table('transaksi')
            ->selectRaw('COUNT(*) as total_transaksi, COALESCE(SUM(grand_total),0) as total_pendapatan, COALESCE(SUM(eco_packaging),0) as eco_count')
            ->where('status','selesai')
            ->whereDate('created_at','>=',$dari)
            ->whereDate('created_at','<=',$sampai)
            ->first();

        $bestSelling = DB::table('detail_transaksi as dt')
            ->join('transaksi as t','dt.id_transaksi','=','t.id_transaksi')
            ->join('menu as m','dt.id_menu','=','m.id_menu')
            ->selectRaw('m.nama_menu, SUM(dt.qty) as total_terjual, SUM(dt.subtotal) as total_revenue')
            ->where('t.status','selesai')
            ->whereDate('t.created_at','>=',$dari)
            ->whereDate('t.created_at','<=',$sampai)
            ->groupBy('m.id_menu','m.nama_menu')
            ->orderByRaw('SUM(dt.qty) DESC')
            ->limit(10)->get();

        $harian = DB::table('transaksi')
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total_transaksi, COALESCE(SUM(grand_total),0) as total_pendapatan')
            ->where('status','selesai')
            ->whereDate('created_at','>=',$dari)
            ->whereDate('created_at','<=',$sampai)
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) ASC')
            ->get();

        $total      = (int) $overview->total_transaksi;
        $ecoCount   = (int) $overview->eco_count;
        $ecoPercent = $total > 0 ? round(($ecoCount / $total) * 100, 1) : 0;
        $plastikKg  = round($ecoCount * 50 / 1000, 2);

        return view('laporan.index', compact(
            'overview','bestSelling','harian',
            'dari','sampai','ecoCount','ecoPercent','plastikKg'
        ));
    }
}