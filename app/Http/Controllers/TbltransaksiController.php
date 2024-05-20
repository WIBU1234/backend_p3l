<?php

namespace App\Http\Controllers;

use App\Models\tblalamat;
use App\Models\tblproduk;
use App\Models\tbltransaksi;
use App\Models\tblcustomer;
use Illuminate\Support\Carbon;
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

    public function listofTransactionToday(){
        try{
            $transaksi = tbltransaksi::with(['tbldetailtransaksi.tblproduk'])
                            ->whereDate('Tanggal_Transaksi', date('Y-m-d'))
                            ->orderBy('status')
                            ->get();

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
            
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function listofTransactionStatusPembayaranValid(){
        try{
            $transaksi = tbltransaksi::with(['tbldetailtransaksi.tblproduk'])
                            ->where('Status', 'Pembayaran Valid')
                            ->get();

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

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function MOAcceptTransaction($id){
        try{
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();

            if ($transaksi == null) {
                return response()->json([
                    'message' => 'History Transaksi tidak ditemukan atau belum dibayar',
                    'data' => null,
                ]);
            }

            if($transaksi->Status != 'Pembayaran Valid'){
                return response()->json([
                    'message' => 'History Transaksi belum dibayarkan',
                    'data' => null,
                ]);
            }

            $customer = tblcustomer::where('ID_Customer', $transaksi->ID_Customer)->first();

            if($customer == null){
                return response()->json([
                    'message' => 'Customer tidak ditemukan || error ID_Customer',
                    'data' => null,
                ]);
            }

            $total_transaksi = $transaksi->Total_Transaksi;
            $points = 0;
        
            if ($total_transaksi >= 1000000) {
                $points = 200;
            } elseif ($total_transaksi >= 500000) {
                $points = 75;
            } elseif ($total_transaksi >= 100000) {
                $points = 15;
            } elseif ($total_transaksi >= 10000) {
                $points = 1;
            }
        
            $transactionDate = Carbon::parse($transaksi->Tanggal_Transaksi);
            $birthday = Carbon::parse($customer->Tanggal_Lahir);
            $startBirthdayPeriod = $birthday->copy()->subDays(3);
            $endBirthdayPeriod = $birthday->copy()->addDays(3);
        
            if ($transactionDate->between($startBirthdayPeriod, $endBirthdayPeriod)) {
                $points *= 2;
            }
        
            // $products = tbltransaksi::with(['tbldetailtransaksi.tblproduk.tblresep.tbldetailresep'])
            //             ->where('ID_Transaksi', $id)
            //             ->get();

            // $products = tbltransaksi::with(['tbldetailtransaksi.tblproduk.tblhampers.tbldetailhampers.tblresep.tbldetailresep'])
            //             ->where('ID_Transaksi', $id)
            //             ->get();

            $products = tbltransaksi::with([
                'tbldetailtransaksi.tblproduk' => function($query) {
                    $query->with([
                        'tblresep.tbldetailresep',
                        'tblhampers' => function($query) {
                            $query->with('tbldetailhampers.tblresep.tbldetailresep');
                        }
                    ]);
                }
            ])->where('ID_Transaksi', $id)->get();
            
            $ingredients = [];
            foreach ($products as $product) {
                foreach ($product['tbldetailtransaksi'] as $detail) {
                    $tblproduk = $detail['tblproduk'];
            
                    // Mengumpulkan bahan-bahan dari tblresep
                    if (isset($tblproduk['tblresep'])) {
                        foreach ($tblproduk['tblresep']['tbldetailresep'] as $resep) {
                            $bahanNama = $resep['Nama_Bahan'];
                            if (isset($ingredients[$bahanNama])) {
                                $ingredients[$bahanNama]['Kuantitas'] += $resep['pivot']['Kuantitas'];
                            } else {
                                $ingredients[$bahanNama] = [
                                    'ID_Bahan_Baku' => $resep['ID_Bahan_Baku'],
                                    'Nama_Bahan' => $bahanNama,
                                    'Stok' => $resep['Stok'],
                                    'Satuan' => $resep['Satuan'],
                                    'Kuantitas' => $resep['pivot']['Kuantitas'],
                                ];
                            }
                        }
                    }
            
                    // Mengumpulkan bahan-bahan dari hampers
                    if (isset($tblproduk['tblhampers'])) {
                        foreach ($tblproduk['tblhampers']['tbldetailhampers'] as $detailHampers) {
                            if (isset($detailHampers['tblresep'])) {
                                foreach ($detailHampers['tblresep']['tbldetailresep'] as $resep) {
                                    $bahanNama = $resep['Nama_Bahan'];
                                    if (isset($ingredients[$bahanNama])) {
                                        $ingredients[$bahanNama]['Kuantitas'] += $resep['pivot']['Kuantitas'];
                                    } else {
                                        $ingredients[$bahanNama] = [
                                            'ID_Bahan_Baku' => $resep['ID_Bahan_Baku'],
                                            'Nama_Bahan' => $bahanNama,
                                            'Stok' => $resep['Stok'],
                                            'Satuan' => $resep['Satuan'],
                                            'Kuantitas' => $resep['pivot']['Kuantitas'],
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            tbltransaksi::where('ID_Transaksi', $id)->update(['Status' => 'diterima']);
            $customer->Poin += $points;
            $customer->save();

            // Ya Allah kodingan apa yang baru saya buat
            return response()->json([
                'message' => 'Transaksi berhasil diterima',
                'data' => [
                    'products' => $products,
                    'ingredients' => $ingredients,
                ],
            ]);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Update Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function MORejectTransaction($id){
        try{
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();

            if ($transaksi == null) {
                return response()->json([
                    'message' => 'History Transaksi tidak ditemukan atau belum dibayar',
                    'data' => null,
                ]);
            }

            if($transaksi->status != 'Pembayaran Valid'){
                return response()->json([
                    'message' => 'History Transaksi belum dibayarkan',
                    'data' => null,
                ]);
            }

            $customer = tblcustomer::where('ID_Customer', $transaksi->ID_Customer)->first();

            if ($customer == null) {
                return response()->json([
                    'message' => 'Customer tidak ditemukan || error ID_Customer',
                    'data' => null,
                ]);
            } 

            $transaksi->Status = 'ditolak';
            $customer->Saldo += $transaksi->Total_Pembayaran;

            $transactionDetails = $transaksi->tbldetailtransaksi;

            foreach ($transactionDetails as $detail) {
                $product = tblproduk::where('ID_Produk', $detail->ID_Produk)->first();

                if ($product != null) {
                    $product->Stok += $detail->Kuantitas;
                    $product->save();
                }
            }

            // $customer->save();
            // $transaksi->save();

            return response()->json([
                'message' => 'Transaksi berhasil ditolak',
                'data' => $transaksi,
            ]);

            $transaksi->delete();

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Update Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }
}
