<?php
namespace App\Http\Controllers;

use App\Models\tblpresensi;
use App\Models\tblpenitip;
use App\Models\tblpengeluaran;
use App\Models\tbltransaksi;
use App\Models\tblpegawai;
use App\Models\tbltransaksibahanbaku;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    private function validateRequest(Request $request, array $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }
        return null;
    }

    private function getDateRange($bulan, $tahun)
    {
        $tgl_awal = date('Y-m-d', strtotime("$tahun-$bulan-01"));
        $tgl_akhir = date('Y-m-t', strtotime($tgl_awal));
        return [$tgl_awal, $tgl_akhir];
    }

    private function formatResponse($tgl_awal, $tahun, $data)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'bulan' => date('F', strtotime($tgl_awal)),
                'tahun' => $tahun,
                'tgl_cetak' => Carbon::now()->format('d F Y'),
                'data' => $data,
            ],
        ], 200);
    }

    private function calculatePresensi($dataPegawai, $totalHari)
    {
        $dataPegawai->each(function($pegawai) use ($totalHari) {
            $pegawai->jumlahPresensi = $pegawai->tblpresensi->count();
            $pegawai->totalHari = $totalHari;

            $pegawai->jumlahHadir = $pegawai->totalHari - $pegawai->jumlahPresensi;
            $pegawai->jumlahBolos = $pegawai->jumlahPresensi;
            $pegawai->honorHarian = ($pegawai->jumlahHadir * 100000);

            if($pegawai->jumlahBolos <= 4){
                $pegawai->bonusRajin = 100000;
                $pegawai->total = $pegawai->honorHarian + $pegawai->bonusRajin;
            } else {
                $pegawai->bonusRajin = 0;
                $pegawai->total = $pegawai->honorHarian;
            }
        });
    }

    private function getPengeluaranAndPenjualan($tgl_awal, $tgl_akhir)
    {
        $pengeluaran = tblpengeluaran::whereBetween('Tanggal', [$tgl_awal, $tgl_akhir])->get();
        $penjualan = tbltransaksi::whereBetween('Tanggal_Transaksi', [$tgl_awal, $tgl_akhir])
            ->where('Status', 'Selesai')
            ->get();
        return [$pengeluaran, $penjualan];
    }

    private function calculateLaporan($pengeluaran, $penjualan, $totalSubTotalBahanBaku, $dataPegawai)
    {
        return [
            'Penjualan' => [
                'Pemasukan' => $penjualan->sum('Total_Transaksi'),
                'Pengeluaran' => 0,
            ],
            'Tip' => [
                'Pemasukan' => $penjualan->sum('Tip'),
                'Pengeluaran' => 0,
            ],
            'Listrik' => [
                'Pemasukan' => 0,
                'Pengeluaran' => $pengeluaran->where('Nama', 'Listrik')->sum('Harga'),
            ],
            'Gaji Karyawan' => [
                'Pemasukan' => 0,
                'Pengeluaran' => $dataPegawai->sum('total'),
            ],
            'Bahan Baku' => [
                'Pemasukan' => 0,
                'Pengeluaran' => $totalSubTotalBahanBaku,
            ],
            'Iuran RT' => [
                'Pemasukan' => 0,
                'Pengeluaran' => $pengeluaran->where('Nama', 'Iuran RT')->sum('Harga'),
            ],
            'Bensin' => [
                'Pemasukan' => 0,
                'Pengeluaran' => $pengeluaran->where('Nama', 'Bensin')->sum('Harga'),
            ],
            'Gas' => [
                'Pemasukan' => 0,
                'Pengeluaran' => $pengeluaran->where('Nama', 'Gas')->sum('Harga'),
            ],
            'Total' => [
                'Pemasukan' => $penjualan->sum('Total_Transaksi') + $penjualan->sum('Tip'),
                'Pengeluaran' => $pengeluaran->where('Nama', 'Listrik')->sum('Harga') + $dataPegawai->sum('total') + $totalSubTotalBahanBaku + $pengeluaran->where('Nama', 'Iuran RT')->sum('Harga') + $pengeluaran->where('Nama', 'Bensin')->sum('Harga') + $pengeluaran->where('Nama', 'Gas')->sum('Harga'),
            ],
        ];
    }

    public function getLaporanPresensi(Request $request)
    {
        $validationError = $this->validateRequest($request, [
            'tgl_awal' => 'required',
            'tgl_akhir' => 'required',
        ]);

        if ($validationError) {
            return $validationError;
        }

        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;

        $data = tblpresensi::with('tblpegawai')
            ->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir])->get();

        return $this->formatResponse($tgl_awal, date('Y', strtotime($tgl_awal)), $data);
    }

    public function getLaporanPresensiByBulanTahun(Request $request)
    {
        try {
            $validationError = $this->validateRequest($request, [
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:1900|max:2100',
            ]);
    
            if ($validationError) {
                return $validationError;
            }
    
            $bulan = $request->bulan;
            $tahun = $request->tahun;
    
            [$tgl_awal, $tgl_akhir] = $this->getDateRange($bulan, $tahun);
    
            $dataPegawai = tblpegawai::with(['tblpresensi' => function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir]);
            }])->whereHas('tblpresensi', function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir]);
            })->get();
    
            $totalHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    
            $this->calculatePresensi($dataPegawai, $totalHari);
    
            return $this->formatResponse($tgl_awal, $tahun, $dataPegawai);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Laporan Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function getLaporanPemasukanPengeluaranBulanan(Request $request)
    {
        try {
            $validationError = $this->validateRequest($request, [
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:1900|max:2100',
            ]);
    
            if ($validationError) {
                return $validationError;
            }
    
            $bulan = $request->bulan;
            $tahun = $request->tahun;
    
            [$tgl_awal, $tgl_akhir] = $this->getDateRange($bulan, $tahun);
        
            [$pengeluaran, $penjualan] = $this->getPengeluaranAndPenjualan($tgl_awal, $tgl_akhir);

            $dataPegawai = tblpegawai::with(['tblpresensi' => function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir]);
            }])->whereHas('tblpresensi', function ($query) use ($tgl_awal, $tgl_akhir) {
                $query->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir]);
            })->get();
    
            $totalHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    
            $this->calculatePresensi($dataPegawai, $totalHari);

            $dataTransaksiBahanBaku = tbltransaksibahanbaku::with('bahanbaku')
                ->whereBetween('Tanggal', [$tgl_awal, $tgl_akhir])
                ->get();

            $totalSubTotalBahanBaku = $dataTransaksiBahanBaku->sum(function($transaksi){
                return $transaksi->bahanbaku->sum('pivot.Sub_Total');
            });

            $tableLaporan = $this->calculateLaporan($pengeluaran, $penjualan, $totalSubTotalBahanBaku, $dataPegawai);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'bulan' => date('F', strtotime($tgl_awal)),
                    'tahun' => $tahun,
                    'tgl_cetak' => Carbon::now()->format('d F Y'),
                    'dataTable' => $tableLaporan,
                    'data' => [
                        $tableLaporan,
                        $pengeluaran,
                        $dataTransaksiBahanBaku,
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Laporan Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function rekapTransaksiPenitipBulan(Request $request)
    {
        try {
            $validationError = $this->validateRequest($request, [
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:1900|max:2100',
            ]);
    
            if ($validationError) {
                return $validationError;
            }
    
            $bulan = $request->bulan;
            $tahun = $request->tahun;
    
            [$tgl_awal, $tgl_akhir] = $this->getDateRange($bulan, $tahun);
    
            $data = tblpenitip::with(['tbltitipan.tblproduk.tbldetailtransaksi.tbltransaksi'])
                ->whereHas('tbltitipan')
                ->get();
    
            $data->each(function($penitipData) {
                $productsSold = [];
                $totalPerolehan = 0;
    
                foreach ($penitipData->tbltitipan as $titipan) {
                    $productName = $titipan->tblproduk->Nama_Produk;
                    $totalKuantitas = 0;
                    $productTotalPerolehan = 0;
                    
                    foreach ($titipan->tblproduk->tbldetailtransaksi as $detailTransaksi) {
                        $totalKuantitas += $detailTransaksi->Kuantitas;
                        if ($detailTransaksi->tbltransaksi) {
                            $productTotalPerolehan += $detailTransaksi->tbltransaksi->Total_Transaksi;
                        }
                    }
    
                    if (!isset($productsSold[$productName])) {
                        $productsSold[$productName] = [
                            'Nama_Produk' => $productName,
                            'Total_Kuantitas' => 0,
                            'Total_Perolehan' => 0
                        ];
                    }
    
                    $productsSold[$productName]['Total_Kuantitas'] += $totalKuantitas;
                    $productsSold[$productName]['Total_Perolehan'] += $productTotalPerolehan;
                }
    
                $penitipData->dataTable = array_values($productsSold);
            });
    
            return $this->formatResponse($tgl_awal, $tahun, $data);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Laporan Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }
}
