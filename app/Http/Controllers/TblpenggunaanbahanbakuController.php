<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TblpenggunaanbahanbakuController extends Controller
{
    public function LaporanPenggunaanBahanBaku($tglAwal, $tglAkhir) {
        try {
            $laporanPBB = DB::table('tblpenggunaanbahanbaku as PBB')
                        ->join('tblbahanbaku as BB', 'PBB.ID_Bahan_Baku', '=', 'BB.ID_Bahan_Baku')
                        ->select(DB::raw('BB.Nama_Bahan, BB.Satuan, SUM(PBB.Kuantitas) as Total_Penggunaan'))
                        ->groupBy('BB.Nama_Bahan', 'BB.Satuan')
                        ->where('PBB.Tanggal', '>=', $tglAwal)
                        ->where('PBB.Tanggal', '<=', $tglAkhir)
                        ->get();

            if ($laporanPBB->count() == 0) {
                return response()->json([
                    'message' => 'Data Laporan Tidak Ditemukan untuk tanggal ' .$tglAwal. ' sampai ' .$tglAkhir,
                    'data' => null,
                ], 404);
            };

            return response()->json([
                'message' => 'Data Laporan Ditemukan',
                'Periode' => $tglAwal . '-' .  $tglAkhir,
                'Tanggal Cetak' => date('Y-m-d'),
                'data' => $laporanPBB,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error On Making Laporan',
                'data' => $e->getMessage(),
            ], 400);
        };
    }
}
