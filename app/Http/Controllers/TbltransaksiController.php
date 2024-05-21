<?php

namespace App\Http\Controllers;

use App\Models\tblalamat;
use App\Models\tbltransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TbltransaksiController extends Controller
{
    public function index()
    {
        $transaksi = tbltransaksi::with(['tblAlamat', 'tbldetailtransaksi.tblproduk'])
            ->get();

        if ($transaksi->count() == 0) {
            return response()->json([
                'message' => 'Transaksi tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Fetch Transaksi Success',
            'data' => $transaksi,
        ], 200);
    }

    public function store(request $request) {
        try {
            $user = Auth::user();
            
            $totalTransaksi = tbltransaksi::count();

            $storeTransaksi = $request->all();
            
            $validate = Validator::make($storeTransaksi, [
                'ID_Pegawai' => 'required',
                'ID_Alamat' => 'required',
                'Total_Transaksi' => 'required',
                'Tanggal_Ambil' => 'required',
            ]);

            if($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                    'status' => 404
                ], 404);
            } else {

                if ($totalTransaksi < 10)
                    $totalTransaksi = '00' . $totalTransaksi;
                else if ($totalTransaksi < 100)
                    $totalTransaksi = '0' . $totalTransaksi;
                else
                    $totalTransaksi = strval($totalTransaksi);

                $storeTransaksi['ID_Transaksi'] = date('y') . '.' . date('m') . '.' . $totalTransaksi;
                $storeTransaksi['ID_Customer'] = $user->ID_Customer;
                $storeTransaksi['Status'] = 'Menunggu Pembayaran';
                $storeTransaksi['Tanggal_Transaksi'] = date('Y-m-d H:i:s');
                $storeTransaksi['Total_Pembayaran'] = 0;

                $transaksi = tbltransaksi::create($storeTransaksi);

                return response([
                    'message' => 'Store Transaksi Success',
                    'data' => $transaksi,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Store Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }


    public function getTransaksiToProduk($id){
        try{
            $transaksi = tbltransaksi::with(['tblAlamat', 'tbldetailtransaksi.tblproduk'])
                ->where('ID_Transaksi', $id)
                ->first();
            
            if(is_null($transaksi)){
                return response()->json([
                    'message' => 'Transaksi tidak ditemukan',
                    'data' => null
                ], 404);
            }
            
            return response()->json([
                'message' => 'Fetch Transaksi Success',
                'data' => $transaksi,
            ], 200);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }                
    }

    public function getTransaksiCustomer() {
        try {
            $user = Auth::user();
            $transaksi = tbltransaksi::join('tbldetailtransaksi', 'tbltransaksi.ID_Transaksi', '=', 'tbldetailtransaksi.ID_Transaksi')
                ->join('tblproduk', 'tbldetailtransaksi.ID_Produk', '=', 'tblproduk.ID_Produk')
                ->with('tblcustomer', 'tblalamat', 'tblpegawai', 'tbldetailtransaksi')
                ->where('tbltransaksi.ID_Customer', $user->ID_Customer)
                ->get(['tbltransaksi.*', 'tblproduk.Nama_Produk']);
    
            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Transaksi tidak ditemukan',
                ]);
            }
    
            return response()->json([
                'message' => 'Transaksi pelanggan berhasil didapatkan',
                'data' => $transaksi,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    
    public function searchDataHistoryTransaksi($nama) {
        try {
            $user = Auth::user();
            $transaksi = tbltransaksi::join('tbldetailtransaksi', 'tbltransaksi.ID_Transaksi', '=', 'tbldetailtransaksi.ID_Transaksi')
                ->join('tblproduk', 'tbldetailtransaksi.ID_Produk', '=', 'tblproduk.ID_Produk')
                ->with('tblcustomer', 'tblalamat', 'tblpegawai', 'tbldetailtransaksi')
                ->where('tbltransaksi.ID_Customer', $user->ID_Customer)
                ->where('tblproduk.Nama_Produk' , 'like' , '%' . $nama . '%')
                ->get(['tbltransaksi.*', 'tblproduk.Nama_Produk']);

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'History Transaksi tidak ditemukan',
                    'data' => null,
                ]);
            }

            return response()->json([
                'message' => 'History Transaksi berhasil didapatkan',
                'data' => $transaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function getTransaksiOnProcess () {
        try {
            $transaksi = tbltransaksi::with('tblpegawai', 'tblcustomer', 'tbljenispengiriman')
                        ->where('Status', 'Menunggu Pembayaran')
                        ->where('Total_Pembayaran', '!=' , 0)
                        ->get();
            if ($transaksi->count() == 0) {
                return response()->json([
                    'message'=> 'Tidak ada transaksi yang sedang berlangsung',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Fetch Transaksi Success',
                'data' => $transaksi,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function updateStatusTransaksi ($id) {
        try {
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();
            if ($transaksi == null) {
                return response()->json([
                    'message' => 'Transaksi Tidak Ditemukan',
                    'data' => null
                ], 404);
            }

            $transaksi->Status = 'Sedang Diproses';
            $transaksi->save();

            return response()->json([
                'message' => 'Update Status Transaksi Success',
                'data' => $transaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update Status Transaksi Failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    } 

    public function showTransaksiNoBayar() {
        try {
            $transaksi = tbltransaksi::join('tblalamat', 'tbltransaksi.ID_Alamat', '=', 'tblalamat.ID_Alamat')
                        ->with('tblpegawai', 'tblcustomer', 'tbljenispengiriman')
                        ->where('Status', 'Menunggu Pembayaran')
                        ->where('ID_JenisPengiriman', '!=', 1)
                        ->where('Total_Bayar', '=', null)
                        ->orderBy('Tanggal_Transaksi')
                        ->get();

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message'=> 'Tidak ada transaksi yang belum dibayar',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Fetch Transaksi Success',
                'data' => $transaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function updateTotalBayarTransaksi ($id) {
        try {
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();
            if ($transaksi == null) {
                return response()->json([
                    'message' => 'Transaksi Tidak Ditemukan',
                    'data' => null
                ], 404);
            }

            $biaya = tblalamat::where('ID_Alamat', $transaksi->ID_Alamat)->first();
            if ($biaya->Biaya == null) {
                return response()->json([
                    'message' => 'Biaya Ongkir belum diinputkan',
                    'data' => null
                ]);
            }

            $transaksi->Total_Bayar = $biaya->Biaya + $transaksi->Total_Transaksi;
            $transaksi->save();

            return response()->json([
                'message' => 'Update Total Bayar Transaksi Success',
                'data' => $transaksi,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update Total Bayar Transaksi Failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
