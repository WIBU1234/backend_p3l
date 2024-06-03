<?php

namespace App\Http\Controllers;

use App\Models\tblhistorysaldo;
use App\Models\tblpresensi;
use App\Models\tblcustomer;
use App\Models\tblpengeluaran;
use App\Models\tbltransaksi;
use App\Models\tblpegawai;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function getLaporanPresensi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tgl_awal' => 'required',
            'tgl_akhir' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;

        $data = tblpresensi::with('tblpegawai')
            ->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir])->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'tgl_awal' => date('F', strtotime($tgl_awal)),
                'tgl_akhir' => date('F', strtotime($tgl_akhir)),
                'data' => $data,
            ],
        ], 200);
    }

    public function getLaporanPresensiByBulanTahun(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:1900|max:2100',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 400);
            }
    
            $bulan = $request->bulan;
            $tahun = $request->tahun;
    
            $tgl_awal = date('Y-m-d', strtotime("$tahun-$bulan-01"));
            $tgl_akhir = date('Y-m-t', strtotime($tgl_awal));
    
            // $data = tblpresensi::with('tblpegawai')
            //     ->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir])->get();
    
            $data = tblpegawai::with(['tblpresensi' => function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir]);
            }])->whereHas('tblpresensi', function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir]);
            })->get();
    
            $totalHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    
            $data->each(function($pegawai) use ($totalHari) {
                $pegawai->jumlahPresensi = $pegawai->tblpresensi->count();
                $pegawai->totalHari = $totalHari;
    
                $pegawai->jumlahHadir = $pegawai->totalHari - $pegawai->jumlahPresensi;
                $pegawai->jumlahBolos = $pegawai->jumlahPresensi;
                $pegawai->honorHarian = ($pegawai->jumlahHadir * 100000);
    
                if($pegawai->jumlahBolos <= 4){
                    $pegawai->bonusRajin = 100000;
                    $pegawai->total = $pegawai->honorHarian + $pegawai->bonusRajin;
                }else{
                    $pegawai->bonusRajin = 0;
                    $pegawai->total = $pegawai->honorHarian;
                }
            });
    
            return response()->json([
                'status' => 'success',
                'data' => [
                    'bulan' => date('F', strtotime($tgl_awal)),
                    'tahun' => $tahun,
                    'tgl_cetak' => Carbon::now()->format('d F Y'),
                    'data' => $data,
                ],
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch Laporan Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function getLaporanPemasukanPengeluaranBulanan(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:1900|max:2100',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 400);
            }
    
            $bulan = $request->bulan;
            $tahun = $request->tahun;
    
            $tgl_awal = date('Y-m-d', strtotime("$tahun-$bulan-01"));
            $tgl_akhir = date('Y-m-t', strtotime($tgl_awal));
        
            $data = tblpengeluaran::whereBetween('Tanggal', [$tgl_awal, $tgl_akhir])->get();
            $dataPenjualan = tbltransaksi::whereBetween('Tanggal_Transaksi', [$tgl_awal, $tgl_akhir])
                ->where('Status', 'Selesai')
                ->get();

            $dataPegawai = tblpegawai::with(['tblpresensi' => function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir]);
            }])->whereHas('tblpresensi', function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir]);
            })->get();
    
            $totalHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    
            $dataPegawai->each(function($pegawai) use ($totalHari) {
                $pegawai->jumlahPresensi = $pegawai->tblpresensi->count();
                $pegawai->totalHari = $totalHari;
    
                $pegawai->jumlahHadir = $pegawai->totalHari - $pegawai->jumlahPresensi;
                $pegawai->jumlahBolos = $pegawai->jumlahPresensi;
                $pegawai->honorHarian = ($pegawai->jumlahHadir * 100000);
    
                if($pegawai->jumlahBolos <= 4){
                    $pegawai->bonusRajin = 100000;
                    $pegawai->total = $pegawai->honorHarian + $pegawai->bonusRajin;
                }else{
                    $pegawai->bonusRajin = 0;
                    $pegawai->total = $pegawai->honorHarian;
                }
            });

            $tableLaporan = [
                'Penjualan' => [
                    'Pemasukan' => $dataPenjualan->sum('Total_Transaksi'),
                    'Pengeluaran' => 0,
                ],
                'Tip' => [
                    'Pemasukan' => $dataPenjualan->sum('Tip'),
                    'Pengeluaran' => 0,
                ],
                'Listrik' => [
                    'Pemasukan' => 0,
                    'Pengeluaran' => $data->where('Nama', 'Listrik')->sum('Harga'),
                ],
                'Gaji Karyawan' => [
                    'Pemasukan' => 0,
                    'Pengeluaran' => $dataPegawai->sum('total'),
                ],
                'total' => 0,
            ];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'bulan' => date('F', strtotime($tgl_awal)),
                    'tahun' => $tahun,
                    'tgl_cetak' => Carbon::now()->format('d F Y'),
                    'dataTable' => $tableLaporan,
                    'data' => 
                        [
                            $tableLaporan,
                            $data,
                            $dataPegawai,
                        ],
                ],
            ], 200);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch Laporan Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

}