<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodWasteLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function getDari(Request $request): string
    {
        return $request->get('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
    }

    private function getSampai(Request $request): string
    {
        return $request->get('tanggal_sampai', now()->format('Y-m-d'));
    }

    // GET /api/laporan/overview
    public function overview(Request $request): JsonResponse
    {
        $dari    = $this->getDari($request);
        $sampai  = $this->getSampai($request);

        $stats = DB::table('transaksi')
            ->selectRaw('
                COUNT(*) AS total_transaksi,
                COALESCE(SUM(grand_total), 0) AS total_pendapatan,
                COALESCE(SUM(pajak), 0) AS total_pajak,
                COALESCE(SUM(total), 0) AS subtotal,
                COALESCE(AVG(grand_total), 0) AS rata_rata,
                COALESCE(SUM(eco_packaging), 0) AS eco_count
            ')
            ->where('status', 'selesai')
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->first();

        $totalItem = DB::table('detail_transaksi as dt')
            ->join('transaksi as t', 'dt.id_transaksi', '=', 't.id_transaksi')
            ->where('t.status', 'selesai')
            ->whereDate('t.created_at', '>=', $dari)
            ->whereDate('t.created_at', '<=', $sampai)
            ->sum('dt.qty');

        $total    = (int) $stats->total_transaksi;
        $ecoCount = (int) $stats->eco_count;

        return response()->json([
            'success' => true,
            'message' => 'Laporan overview berhasil diambil.',
            'data'    => [
                'periode'    => ['dari' => $dari, 'sampai' => $sampai],
                'transaksi'  => [
                    'total'              => $total,
                    'total_item_terjual' => (int) $totalItem,
                    'rata_rata'          => 'Rp ' . number_format((int) $stats->rata_rata, 0, ',', '.'),
                ],
                'pendapatan' => [
                    'subtotal'    => (int) $stats->subtotal,
                    'pajak'       => (int) $stats->total_pajak,
                    'grand_total' => (int) $stats->total_pendapatan,
                    'grand_total_formatted' => 'Rp ' . number_format((int) $stats->total_pendapatan, 0, ',', '.'),
                ],
                'sdgs' => [
                    'eco_packaging_count'      => $ecoCount,
                    'eco_packaging_percentage' => $total > 0 ? round(($ecoCount / $total) * 100, 1) : 0,
                ],
            ],
        ]);
    }

    // GET /api/laporan/best-selling
    public function bestSelling(Request $request): JsonResponse
    {
        $dari   = $this->getDari($request);
        $sampai = $this->getSampai($request);
        $limit  = min((int) $request->get('limit', 10), 50);

        $data = DB::table('detail_transaksi as dt')
            ->join('transaksi as t', 'dt.id_transaksi', '=', 't.id_transaksi')
            ->join('menu as m', 'dt.id_menu', '=', 'm.id_menu')
            ->leftJoin('kategori as k', 'm.id_kategori', '=', 'k.id_kategori')
            ->selectRaw('
                m.id_menu, m.nama_menu, m.harga, k.nama_kategori,
                SUM(dt.qty) AS total_terjual,
                SUM(dt.subtotal) AS total_revenue
            ')
            ->where('t.status', 'selesai')
            ->whereDate('t.created_at', '>=', $dari)
            ->whereDate('t.created_at', '<=', $sampai)
            ->groupBy('m.id_menu', 'm.nama_menu', 'm.harga', 'k.nama_kategori')
            ->orderByRaw('SUM(dt.qty) DESC')
            ->limit($limit)
            ->get()
            ->map(fn($row, $i) => [
                'ranking'        => $i + 1,
                'nama_menu'      => $row->nama_menu,
                'kategori'       => $row->nama_kategori ?? '-',
                'total_terjual'  => (int) $row->total_terjual,
                'total_revenue'  => 'Rp ' . number_format((int) $row->total_revenue, 0, ',', '.'),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu terlaris berhasil diambil.',
            'data'    => $data,
        ]);
    }

    // GET /api/laporan/harian
    public function harian(Request $request): JsonResponse
    {
        $dari   = $this->getDari($request);
        $sampai = $this->getSampai($request);

        $data = DB::table('transaksi')
            ->selectRaw('
                DATE(created_at) AS tanggal,
                COUNT(*) AS total_transaksi,
                COALESCE(SUM(grand_total), 0) AS total_pendapatan,
                COALESCE(SUM(eco_packaging), 0) AS eco_count
            ')
            ->where('status', 'selesai')
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) ASC')
            ->get()
            ->map(fn($row) => [
                'tanggal'          => $row->tanggal,
                'total_transaksi'  => (int) $row->total_transaksi,
                'total_pendapatan' => 'Rp ' . number_format((int) $row->total_pendapatan, 0, ',', '.'),
                'eco_count'        => (int) $row->eco_count,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan harian berhasil diambil.',
            'data'    => $data,
        ]);
    }

    // GET /api/laporan/sustainability
    public function sustainability(Request $request): JsonResponse
    {
        $dari   = $this->getDari($request);
        $sampai = $this->getSampai($request);

        $ecoStats = DB::table('transaksi')
            ->selectRaw('
                COUNT(*) AS total,
                COALESCE(SUM(eco_packaging), 0) AS eco_count
            ')
            ->where('status', 'selesai')
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->first();

        $wasteStats = DB::table('food_waste_log as fw')
            ->join('menu as m', 'fw.id_menu', '=', 'm.id_menu')
            ->selectRaw('
                COALESCE(SUM(fw.jumlah), 0) AS total_unit,
                COALESCE(SUM(fw.jumlah * m.harga), 0) AS total_kerugian,
                COUNT(*) AS total_kejadian
            ')
            ->whereDate('fw.created_at', '>=', $dari)
            ->whereDate('fw.created_at', '<=', $sampai)
            ->first();

        $total    = (int) $ecoStats->total;
        $ecoCount = (int) $ecoStats->eco_count;
        $ecoPercentage = $total > 0 ? round(($ecoCount / $total) * 100, 1) : 0;
        $plastikKg = round($ecoCount * 50 / 1000, 3);

        $ecoScore   = min($ecoPercentage, 100);
        $wasteScore = (int) $wasteStats->total_kejadian === 0
            ? 100
            : max(0, 100 - (int)(ceil((int)$wasteStats->total_kejadian / 5) * 10));
        $totalScore = round(($ecoScore * 0.6) + ($wasteScore * 0.4), 1);

        $grade = match(true) {
            $totalScore >= 85 => 'A — Excellent',
            $totalScore >= 70 => 'B — Good',
            $totalScore >= 55 => 'C — Fair',
            $totalScore >= 40 => 'D — Needs Improvement',
            default           => 'E — Poor',
        };

        return response()->json([
            'success' => true,
            'message' => 'Laporan sustainability berhasil diambil.',
            'data'    => [
                'periode'       => ['dari' => $dari, 'sampai' => $sampai],
                'sdgs_score'    => ['total' => $totalScore, 'grade' => $grade],
                'eco_packaging' => [
                    'count'            => $ecoCount,
                    'percentage'       => $ecoPercentage,
                    'plastik_dikurangi'=> $plastikKg . ' kg',
                ],
                'food_waste'    => [
                    'total_unit'     => (int) $wasteStats->total_unit,
                    'total_kerugian' => 'Rp ' . number_format((int) $wasteStats->total_kerugian, 0, ',', '.'),
                    'total_kejadian' => (int) $wasteStats->total_kejadian,
                ],
                'sdgs_goals'    => [
                    ['goal' => 'SDG 12', 'status' => $ecoPercentage >= 30 ? 'on_track' : 'needs_improvement'],
                    ['goal' => 'SDG 13', 'status' => $ecoPercentage >= 20 ? 'on_track' : 'needs_improvement'],
                    ['goal' => 'SDG 2',  'status' => 'monitored'],
                ],
            ],
        ]);
    }

    // POST /api/laporan/food-waste
    public function storeFoodWaste(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_menu' => 'required|integer|exists:menu,id_menu',
            'jumlah'  => 'required|integer|min:1',
            'alasan'  => 'required|in:kadaluarsa,rusak,sisa_hari,lainnya',
            'catatan' => 'nullable|string|max:500',
        ]);

        $log = FoodWasteLog::create($validated);
        $log->load('menu:id_menu,nama_menu');

        return response()->json([
            'success' => true,
            'message' => 'Food waste berhasil dicatat.',
            'data'    => $log,
        ], 201);
    }
}