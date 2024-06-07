<?php

namespace App\Http\Controllers;

use App\Models\tblpenggunaanbahanbaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TblpenggunaanbahanbakuController extends Controller
{
    public function index() {
        $materials = tblpenggunaanbahanbaku::with(['tblbahanbaku'])
                    ->get();

        // $groupedMaterial = [];
        // foreach ($materials as $material) {
        //     $tanggal = $material->Tanggal;
        //     $id_bahan = $material->tblbahanbaku->Nama_Bahan;

        //     if (!isset($groupedMaterial[$tanggal])) {
        //         $groupedMaterial[$tanggal] = [];
        //     }

        //     if (!isset($groupedMaterial[$tanggal][$id_bahan])) {
        //         $groupedMaterial[$tanggal][$id_bahan] = [
        //             'Kuantitas' => 0,
        //             'Satuan' => $material->tblbahanbaku->Satuan
        //         ];
        //     }

        //     $groupedMaterial[$tanggal][$id_bahan]['Kuantitas'] += $material->Kuantitas;
        // }

        if(count($materials) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $materials
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }
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

            $laporanFix = ([
                'Periode' => $tglAwal . '-' .  $tglAkhir,
                'Tanggal_Cetak' => date('Y-m-d'),
                'data' => $laporanPBB,
            ]);
            

            return response()->json([
                'message' => 'Data Laporan Ditemukan',
                'data' => $laporanFix,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error On Making Laporan',
                'data' => $e->getMessage(),
            ], 400);
        };
    }
}
