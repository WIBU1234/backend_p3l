<?php

namespace App\Http\Controllers;

use App\Models\tblalamat;
use App\Models\tblpegawai;
use App\Models\tblpenggunaanbahanbaku;
use App\Models\tblproduk;
use App\Models\tbltransaksi;
use App\Models\tblcustomer;
use App\Models\tblbahanbaku;
use App\Models\tbldetailtransaksi;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TbltransaksiController extends Controller
{
    public function index()
    {
        $transaksi = tbltransaksi::with(['tblalamat', 'tblcustomer', 'products', 'tbldetailtransaksi.tblproduk'])
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
            $lusa = Carbon::now()->addDays(2)->format('Y-m-d');

            $user = Auth::user();

            $storeTransaksi = $request->all();

            //Dapetin Pegawai Admin
            $pegawai = tblpegawai::where('ID_Jabatan', 2)->first();
            
            $validate = Validator::make($storeTransaksi, [
                'Poin' => 'required',
                'products' => 'required',
                'Tanggal_Ambil' => 'required',
                'Total_Transaksi' => 'required',
                'ID_Alamat' => 'required',
                'ID_JenisPengiriman' => 'required'
            ]);

            if($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                    'status' => 404
                ], 404);
            } 

            $totalHarga = $storeTransaksi['Total_Transaksi'];

            $storeTransaksi['ID_Transaksi'] = $this->generateIDTrans();
            $storeTransaksi['ID_Customer'] = $user->ID_Customer;
            $storeTransaksi['ID_Pegawai'] = $pegawai->ID_Pegawai;
            if ($storeTransaksi['ID_JenisPengiriman'] == 3) {
                $storeTransaksi['Status'] = 'Menunggu Konfirmasi Admin';
            } else {
                $storeTransaksi['Status'] = 'Menunggu Pembayaran';
                $storeTransaksi['Total_Bayar'] = $totalHarga;
            }

            $storeTransaksi['Tanggal_Transaksi'] = date('Y-m-d H:i:s');
            $storeTransaksi['Tipe_Transaksi'] = 0;

            $transaksi = tbltransaksi::create($storeTransaksi);
            if ($request->has('products')) {
                $products = $request->input('products');
    
                $productsData = [];
                foreach ($products as $index => $data) {
                    
                    $productsData[$index] = [
                        'ID_Produk' => $data['ID_Produk'],
                        'Tipe' => $data['Tipe'],
                        'Kuantitas' => $data['Kuantitas'],
                        'Sub_Total' => $data['Sub_Total'] 
                    ];
                }
    
                foreach ($productsData as $data) {
                    $transaksi->products()->attach($data['ID_Produk'], [
                        'Tipe' => $data['Tipe'],
                        'Kuantitas' => $data['Kuantitas'],
                        'Sub_Total' => $data['Sub_Total']
                    ]);
                }
            }

            $this->reducePoin($storeTransaksi['Poin']);

            if ($transaksi->save()) {
                return response([
                    'message' => 'Store Transaksi Success',
                    'status' => $transaksi->Status,
                    'poin' => $user->Poin,
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

    public function storeReady(request $request) {
        try {
            //$lusa = Carbon::now()->addDays(2);
            $today = Carbon::now();

            $user = Auth::user();

            $storeTransaksi = $request->all();

            //Dapetin Pegawai Admin
            $pegawai = tblpegawai::where('ID_Jabatan', 2)->first();
            
            $validate = Validator::make($storeTransaksi, [
                'Poin' => 'required',
                'products' => 'required',
                'Total_Transaksi' => 'required',
                'ID_Alamat' => 'required',
                'ID_JenisPengiriman' => 'required'
            ]);

            if($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                    'status' => 404
                ], 404);
            } 

            $storeTransaksi['ID_Transaksi'] = $this->generateIDTrans();
            $storeTransaksi['ID_Customer'] = $user->ID_Customer;
            $storeTransaksi['ID_Pegawai'] = $pegawai->ID_Pegawai;
            $totalHarga = $storeTransaksi['Total_Transaksi'];

            if ($storeTransaksi['ID_JenisPengiriman'] == 3) {
                $storeTransaksi['Status'] = 'Menunggu Konfirmasi Admin';
            } else {
                $storeTransaksi['Status'] = 'Menunggu Pembayaran';
                $storeTransaksi['Total_Bayar'] = $totalHarga;
            }
            $storeTransaksi['Tanggal_Transaksi'] = date('Y-m-d H:i:s');
            $storeTransaksi['Tanggal_Ambil'] = $today;
            $storeTransaksi['Tipe_Transaksi'] = 1;

            $transaksi = tbltransaksi::create($storeTransaksi);
            if ($request->has('products')) {
                $products = $request->input('products');
    
                $productsData = [];
                foreach ($products as $index => $data) {
                    
                    $productsData[$index] = [
                        'ID_Produk' => $data['ID_Produk'],
                        'Tipe' => $data['Tipe'],
                        'Kuantitas' => $data['Kuantitas'],
                        'Sub_Total' => $data['Sub_Total'] 
                    ];
                }
    
                foreach ($productsData as $data) {
                    $transaksi->products()->attach($data['ID_Produk'], [
                        'Tipe' => $data['Tipe'],
                        'Kuantitas' => $data['Kuantitas'],
                        'Sub_Total' => $data['Sub_Total']
                    ]);
                }
            }

            $this->reducePoin($storeTransaksi['Poin']);

            if ($transaksi->save()) {
                return response([
                    'message' => 'Store Transaksi Success',
                    'today' => $today,
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

    private function deleteStock($produkID, $quantity) {
        $produk = tblproduk::find($produkID);

        if ($produk === null) {
            return;
        } 

        if ($produk->ID_Kategori === 4) {
            $produk->StokReady -= $quantity;
        }

        $produk->save();
    }

    private function deleteReadyStock($produkID, $quantity) {
        $produk = tblproduk::find($produkID);

        if ($produk === null) {
            return;
        } 

        if ($produk->StokReady !== 0) {
            $produk->StokReady -= $quantity;
        }

        $produk->save();
    }

    // public function addReady($transID) {
    //     $transaksi = tbltransaksi::find($transID)->first();

        
    // }

    private function addReadyStock($produkID, $quantity) {
        $produk = tblproduk::find($produkID);

        if ($produk === null) {
            return;
        } 

        if ($produk->StokReady !== 0) {
            $produk->StokReady += $quantity;
            $produk->save();
        }
    }

    private function generateIDTrans()
    {
        $year = date('y');
        $month = date('m');

        $latestTransaksi = tbltransaksi::latest('ID_Transaksi')->first();
        if ($latestTransaksi) {
            $lastID = $latestTransaksi->ID_Transaksi;
            $parts = explode('.', $lastID);
            $index = intval($parts[2]);
            $newID = $index + 1;
        } else {
            $newID = 1;
        }

        // Make Sure gak ada ID kedoble
        do {
            $id = $year . '.' . $month . '.' . $newID;
            $existingTrans = tbltransaksi::where('ID_Transaksi', $year . '.' . $month . '.' . $id)->first();
            $newID++;
        } while ($existingTrans);

        return $id;
    }

    public function countPoin(String $id_trans) //Buat MO
    {
        $transaksi = tbltransaksi::where('ID_Transaksi', $id_trans)->first();
        $totalHarga = $transaksi->Total_Transaksi;
        $poin = 0;

        
        // Setiap pemesanan dengan kelipatan 1.000.000 mendapatkan 200 poin
        $poin += intdiv($totalHarga, 1000000) * 200;
        $totalHarga %= 1000000;
        
        // Setiap pemesanan dengan kelipatan 500.000 mendapatkan 75 poin.
        $poin += intdiv($totalHarga, 500000) * 75;
        $totalHarga %= 500000;

        // Setiap pemesanan dengan kelipatan 100.000 mendapatkan 15 poin.
        $poin += intdiv($totalHarga, 100000) * 15;
        $totalHarga %= 100000;

        // Setiap pemesanan dengan kelipatan 10.000 mendapatkan 1 poin.
        $poin += intdiv($totalHarga, 10000) * 1;

        return $poin;
    }

    private function reducePoin($poin)
    {
        $user = Auth::user();

        if ($user->Poin > $poin) {
            $user->Poin -= $poin;
        }

        $user->save();
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
                    'data' => null,
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
                        // ->where('ID_JenisPengiriman', '!=', 1)
                        ->where('Bukti_Pembayaran', '!=', null)
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

            $transaksi->Status = 'Pembayaran Valid';
            $transaksi->Tip = $transaksi->Total_pembayaran - $transaksi->Total_Bayar;
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
                        ->where('tblalamat.Jarak', '!=', 0)
                        ->where('tbltransaksi.Status', 'Menunggu Konfirmasi Admin')
                        ->where('ID_JenisPengiriman', '>=', 1)
                        ->where('Total_Bayar', '=', null)
                        ->where('Bukti_Pembayaran', '=', null)
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
            $transaksi->Status = 'Menunggu Pembayaran';
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
            $transaksi = tbltransaksi::with(['tblcustomer'])
                    ->whereIn('Status', ['Pembayaran Valid', 'diterima'])
                    ->orderBy('Tanggal_Transaksi', 'asc')
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

    public function MOAcceptTransaction($id) {
        try {
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();
    
            if ($transaksi == null) {
                return response()->json([
                    'message' => 'History Transaksi tidak ditemukan',
                    'data' => null,
                ]);
            }

            if ($transaksi->Status == 'diterima') {
                return response()->json([
                    'message' => 'History Transaksi sudah pernah diterima MO',
                    'data' => null,
                ]);
            }

            if ($transaksi->Status != 'Pembayaran Valid') {
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
    
            $total_transaksi = $transaksi->Total_Transaksi;
            $points = $this->calculatePoints($total_transaksi, $transaksi->Tanggal_Transaksi, $customer->Tanggal_Lahir);
    
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
    
            $ingredients = $this->collectIngredients($products);

            foreach ($ingredients as $ingredient) {
                tblpenggunaanbahanbaku::create([
                    'ID_Bahan_Baku' => $ingredient['ID_Bahan_Baku'],
                    'Kuantitas' => $ingredient['Kuantitas'],
                    'Tanggal' => Carbon::now(),
                ]);
                $iniBahanBaku = tblbahanbaku::where('ID_Bahan_Baku', $ingredient['ID_Bahan_Baku'])->first();

                tblbahanbaku::where('ID_Bahan_Baku', $ingredient['ID_Bahan_Baku'])
                    ->first()
                    ->update(['Stok' => $iniBahanBaku->Stok - $ingredient['Kuantitas']]);
            }

            tbltransaksi::where('ID_Transaksi', $id)->update(['Status' => 'diterima']);
            $customer->Poin += $points;
            $customer->save();


            return response()->json([
                'message' => 'Transaksi berhasil diterima',
                'data' => [
                    'products' => $products,
                    'ingredients' => $ingredients,
                ],
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function getAllIngredientsAndProduct($id){
        try{
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();
    
            if ($transaksi == null) {
                return response()->json([
                    'message' => 'History Transaksi tidak ditemukan',
                    'data' => null,
                ]);
            }

            if ($transaksi->Status == 'diterima') {
                return response()->json([
                    'message' => 'History Transaksi sudah pernah diterima MO',
                    'data' => null,
                ]);
            }

            if ($transaksi->Status != 'Pembayaran Valid') {
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
    
            $total_transaksi = $transaksi->Total_Transaksi;
    
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
    
            $ingredients = $this->collectIngredients($products);
    
            return response()->json([
                'message' => 'Transaksi berhasil diterima',
                'data' => [
                    'products' => $products,
                    'ingredients' => $ingredients,
                ],
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function getIngredientsOfProduct($id){
        try{
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();
    
            // if ($transaksi == null) {
            //     return response()->json([
            //         'message' => 'History Transaksi tidak ditemukan',
            //         'data' => null,
            //     ]);
            // }

            // if ($transaksi->Status != 'diterima') {
            //     return response()->json([
            //         'message' => 'Transaksi belum diterima MO',
            //         'data' => $transaksi,
            //     ]);
            // }
    
            $customer = tblcustomer::where('ID_Customer', $transaksi->ID_Customer)->first();
    
            if ($customer == null) {
                return response()->json([
                    'message' => 'Customer tidak ditemukan || error ID_Customer',
                    'data' => null,
                ]);
            }
    
            $total_transaksi = $transaksi->Total_Transaksi;
    
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
    
            $ingredients = $this->collectIngredients($products);
    
            return response()->json([
                'message' => 'Transaksi berhasil diterima',
                'data' => [
                    'products' => $products,
                    'ingredients' => $ingredients,
                ],
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    private function calculatePoints($total_transaksi, $transactionDate, $birthday) {
        $points = 0;
        $totalBiayaBayar = $total_transaksi;

        while($totalBiayaBayar > 0){
            if ($totalBiayaBayar >= 1000000) {
                $points += 200;
                $totalBiayaBayar -= 1000000;
            } elseif ($totalBiayaBayar >= 500000) {
                $points += 75;
                $totalBiayaBayar -= 500000;
            } elseif ($totalBiayaBayar >= 100000) {
                $points += 15;
                $totalBiayaBayar -= 100000;
            } elseif($totalBiayaBayar < 10000){
                break;
            }else {
                $points += 1;
                $totalBiayaBayar -= 10000;
            }
        }

        $transactionDate = Carbon::parse($transactionDate);
        $birthday = Carbon::parse($birthday);
        $startBirthdayPeriod = $birthday->copy()->subDays(3);
        $endBirthdayPeriod = $birthday->copy()->addDays(3);
    
        if ($transactionDate->between($startBirthdayPeriod, $endBirthdayPeriod)) {
            $points *= 2;
        }
    
        return $points;
    }
    
    private function collectIngredients($products) {
        $ingredients = [];
        
        foreach ($products as $product) {
            foreach ($product['tbldetailtransaksi'] as $detail) {
                $tblproduk = $detail['tblproduk'];
                $kuantitasProduk = $detail['Kuantitas'];
                
                // Mengumpulkan bahan-bahan dari tblresep
                if (isset($tblproduk['tblresep'])) {
                    $ingredients = $this->collectIngredientsFromRecipe($tblproduk['tblresep']['tbldetailresep'], $ingredients, $kuantitasProduk);
                }
                
                // Mengumpulkan bahan-bahan dari hampers
                if (isset($tblproduk['tblhampers'])) {
                    foreach ($tblproduk['tblhampers']['tbldetailhampers'] as $detailHampers) {
                        if($detailHampers['Kuantitas'] < 1){
                            $kuantitasForHampers = $kuantitasProduk;    
                        }else{
                            $kuantitasForHampers = $kuantitasProduk * $detailHampers['Kuantitas'];
                        }

                        if (isset($detailHampers['tblresep'])) {
                            $ingredients = $this->collectIngredientsFromRecipe($detailHampers['tblresep']['tbldetailresep'], $ingredients, $kuantitasForHampers);
                        }
                    }
                }
            }
        }
        return $ingredients;
    }
    
    private function collectIngredientsFromRecipe($recipeDetails, $ingredients, $kuantitasProduk) {
        foreach ($recipeDetails as $resep) {
            $bahanNama = $resep['Nama_Bahan'];
            $kuantitasBahan = $resep['pivot']['Kuantitas'] * $kuantitasProduk;
            
            if (isset($ingredients[$bahanNama])) {
                $ingredients[$bahanNama]['Kuantitas'] += $kuantitasBahan;
            } else {
                $ingredients[$bahanNama] = [
                    'ID_Bahan_Baku' => $resep['ID_Bahan_Baku'],
                    'Nama_Bahan' => $bahanNama,
                    'Stok' => $resep['Stok'],
                    'Satuan' => $resep['Satuan'],
                    'Kuantitas' => $kuantitasBahan,
                ];
            }
        }
        return $ingredients;
    }
    
    
    public function MORejectTransaction($id){
        try{
            $transaksi = tbltransaksi::with('tbldetailtransaksi')
                ->where('ID_Transaksi', $id)->first();

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

            if ($customer == null) {
                return response()->json([
                    'message' => 'Customer tidak ditemukan || error ID_Customer',
                    'data' => null,
                ]);
            } 

            $transactionDetails = $transaksi->tbldetailtransaksi;

            foreach ($transactionDetails as $detail) {
                tbldetailtransaksi::where('ID_Transaksi', $id)->update(['Kuantitas' => 0]);
            }

            tbltransaksi::where('ID_Transaksi', $id)->update(['Status' => 'ditolak']);            
            tblcustomer::where('ID_Customer', $transaksi->ID_Customer)->update(['Saldo' => $customer->Saldo + $transaksi->Total_pembayaran]);
            $transaksi = tbltransaksi::with('tbldetailtransaksi')
                ->where('ID_Transaksi', $id)->first();
            $customer = tblcustomer::
                where('ID_Customer', $transaksi->ID_Customer)->first();

            return response()->json([
                'message' => 'Transaksi berhasil ditolak',
                'data' => [
                    'customer' => $customer,
                    'transaction' => $transaksi,
                ],
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Update Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function getCompleteTransCust() {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated',
                'data' => null
            ], 401);
        }

        $transaksi = tbltransaksi::where('ID_Customer', $user->ID_Customer)
                                ->where('Status', 'Selesai')->with(['tblcustomer', 'tblalamat', 'products', 'tbljenispengiriman'])->get();

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

    public function getTransaksiByIdCustomer($id){
        try{
            $date = Carbon::now();

            $transaksi = tbltransaksi::with(['tblAlamat', 'tbldetailtransaksi.tblproduk', 'tblcustomer'])
                ->where('ID_Customer', $id)
                // ->where('Status', 'Menunggu Pembayaran')
                // ->whereDate('Tanggal_Transaksi', '<', $date)
                ->orderBy('Tanggal_Transaksi', 'asc')
                ->get();

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Transaksi tidak ditemukan',
                    'data' => null,
                ]);
            }

            return response()->json([
                'message' => 'Transaksi berhasil diambil',
                'data' => $transaksi,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function sendProofPayment(request $request){
        try{
            $user = Auth::user();
            $validator = Validator::make($request->all(), [
                'ID_Customer' => 'required',
                'ID_Transaksi' => 'required',
                'Bukti_Pembayaran' => 'required|image|max:2048',
                'Total_pembayaran' => 'required|numeric',
            ]);
    
            if($validator->fails()) {
                return response([
                    'message' => $validator->errors(),
                    'status' => 404
                ], 404);
            }

            $IDTransaksi = $request->ID_Transaksi;
            $totalPembayaranInput = $request->Total_pembayaran;
            $transaksi = tbltransaksi::get()->where('ID_Transaksi', $IDTransaksi)->first();

            if($transaksi == null){
                return response([
                    'message' => "Transaksi tidak ditemukan",
                    'status' => 404
                ], 404);
            }

            $image = $request->file('Bukti_Pembayaran');
            $originalName = $image->getClientOriginalName();
            $cloudinaryController = new cloudinaryController();
            $public_id = $cloudinaryController->sendImageToCloudinary($image, $originalName);

            tbltransaksi::where('ID_Transaksi', $IDTransaksi)->update([
                'Bukti_Pembayaran' => $public_id,
                'Total_pembayaran' => $totalPembayaranInput,
            ]);

            return response([
                'message' => 'Bukti Pembayaran berhasil dikirim',
                'status' => 200,
                'data' => $transaksi
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Sending proof failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function ShowTransaksiDiproses () {
        try {
            $transaksi = tbltransaksi::join('tblcustomer', 'tbltransaksi.ID_Customer', '=', 'tblcustomer.ID_Customer')
                    ->join('tbljenispengiriman', 'tbltransaksi.ID_JenisPengiriman', '=', 'tbljenispengiriman.ID_JenisPengiriman')
                    ->where('tbltransaksi.Status', '=', 'Diproses')
                    ->get();

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Transaksi dalam Status Sedang Diproses Tidak ada',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Berhasil Mendapatkan Transaksi dalam Status Sedang Diproses',
                'data' => $transaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function UpdateStatusKirimTransaksi ($id) {
        try {
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();

            if ($transaksi == null) {
                return response()->json([
                    'message' => 'Data Transaksi Tidak Ditemukan',
                    'data' => null,
                ], 404);
            }

            if($transaksi->ID_JenisPengiriman == 1) {
                $transaksi->Status = 'Siap Dipick-Up';
            } else {
                $transaksi->Status = 'Siap Dikirim';
            }

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

    public function ShowTransaksiSiapKirim () {
        try {
            $transaksi = tbltransaksi::join('tblcustomer', 'tbltransaksi.ID_Customer', '=', 'tblcustomer.ID_Customer')
                    ->join('tbljenispengiriman', 'tbltransaksi.ID_JenisPengiriman', '=', 'tbljenispengiriman.ID_JenisPengiriman')
                    ->where('tbltransaksi.Status', '=', 'Siap Dipick-Up')
                    ->orWhere('tbltransaksi.Status', '=', 'Siap Dikirim')
                    ->orWhere('tbltransaksi.Status', '=', 'diterima')
                    ->get();

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Transaksi dalam Status Sedang Diproses Tidak ada',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Berhasil Mendapatkan Transaksi dalam Status Sedang Diproses',
                'data' => $transaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function UpdateStatusSelesaiTransaksi ($id) {
        try {
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();

            if ($transaksi == null) {
                return response()->json([
                    'message' => 'Data Transaksi Tidak Ditemukan',
                    'data' => null,
                ], 404);
            }

            if($transaksi->ID_JenisPengiriman == 1) {
                $transaksi->Status = 'Selesai';
            } else {
                $transaksi->Status = 'Dibawa Kurir';
            }

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

    public function ShowTransaksiSelesai() {
        try {
            $user = Auth::user();
            $transaksi = tbltransaksi::where('ID_Customer', $user->ID_Customer)
                            ->where(function ($query) {
                                $query->where('Status', 'Dibawa Kurir')
                                      ->orWhere('Status', 'Selesai');
                            })->get();
    
            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Data Transaksi Kosong',
                    'data' => null,
                ], 404);
            }
    
            return response()->json([
                'message' => 'Berhasil Mendapatkan Data Transaksi',
                'data' => $transaksi,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }    

    public function ShowTransaksiDibawaKurir() {
        try {
            $user = Auth::user();
            $transaksi = tbltransaksi::where('ID_Customer', $user->ID_Customer)
                        ->Where('Status', 'Dibawa Kurir')
                        ->get();

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Data Transaksi Kosong',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Berhasil Mendapatkan Data Transaksi',
                'data' => $transaksi,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Fetch Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function UpdateTransaksiSelesaiCustomer($id) {
        try {
            $user = Auth::user();
            $transaksi = tbltransaksi::where('ID_Customer', $user->ID_Customer)
                        ->where('ID_Transaksi', $id)
                        ->first();
            var_dump($transaksi);
            if ($transaksi->count() === 0) {
                return response()->json([
                    'message' => 'Data Transaksi Tidak Ditemukan',
                    'data' => null,
                ], 404);
            }

            if ($transaksi->Status != 'Dibawa Kurir') {
                return response()->json([
                    'message' => 'Status Transaksi Belum Dibawa Kurir',
                    'data' => null,
                ], 404);
            }

            $transaksi->Status = 'Selesai';
            $transaksi->save();

            return response()->json([
                'message' => 'Update Status Transaksi Success',
                'data' => $transaksi,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error Updating Data Customer',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function showTransaksiExpired() {
        try {
            $transaksi = tbltransaksi::whereRaw('Tanggal_Pelunasan > DATE_SUB(Tanggal_Ambil, INTERVAL 1 DAY)')
                        ->where('Status', '=', 'Menunggu Pembayaran')
                        ->get();
            
            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Data Transaksi Telat Bayar Kosong',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Berhasil Mendapatkan Data Transaksi Telat Bayar',
                'data' => $transaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error Get Data Transaksi Expired',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function PutTransaksiTelatBayar($id) {
        try {
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Tidak Menemukan Data Transaksi dengan ID ' . $id,
                    'data' => null,
                ], 404);
            }

            $transactionDetails = $transaksi->tbldetailtransaksi;

            foreach ($transactionDetails as $detail) {
                tbldetailtransaksi::where('ID_Transaksi', $id)->update(['Kuantitas' => 0]);
            }

            $transaksi->Status = 'Batal';
            $transaksi->save();

            return response()->json([
                'message' => 'Berhasil Mengupdate Data Transaksi dengan ID ' . $id,
                'data' => $transaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error Update Data Transaksi Expired',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function ProcessingTransaction() {
        try {
            $tommorow = new DateTime('tomorrow');
            $tommorow->format('Y-m-d');

            $transaksi = tbltransaksi::whereIn('Status', ['Menunggu Pembayaran', 'Pembayaran Valid', 'Menunggu Konfirmasi Admin', 'diterima'])
                                    ->whereNotIn('Status', ['Ditolak', 'Diproses', 'Siap Di-Pickup', 'Sedang Dikirim Kurir', 'Sudah Di-Pickup', 'Selesai'])
                                    ->where('Tipe_Transaksi', 0)
                                    ->where('Tanggal_Ambil', $tommorow)
                                    ->with(['tblcustomer', 'products', 'products.tblresep', 'products.tblresep.tbldetailresep'])->get();

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Transaksi tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Fetch Data Success',
                'date' => $tommorow,
                'data' => $transaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error Update Data Transaksi Expired',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function MOChangeToDiproses($id){
        try{
            $transaksi = tbltransaksi::where('ID_Transaksi', $id)->first();

            if ($transaksi == null) {
                return response()->json([
                    'message' => 'History Transaksi tidak ditemukan',
                    'data' => null,
                ]);
            }

            //$total_transaksi = $transaksi->Total_Transaksi;
            //$points = $this->calculatePoints($total_transaksi, $transaksi->Tanggal_Transaksi, $customer->Tanggal_Lahir);
    
            // $products = tbltransaksi::with([
            //     'tbldetailtransaksi.tblproduk' => function($query) {
            //         $query->with([
            //             'tblresep.tbldetailresep',
            //             'tblhampers' => function($query) {
            //                 $query->with('tbldetailhampers.tblresep.tbldetailresep');
            //             }
            //         ]);
            //     }
            // ])->where('ID_Transaksi', $id)->get();
    
            // $ingredients = $this->collectIngredients($products);

            // foreach ($ingredients as $ingredient) {
            //     tblpenggunaanbahanbaku::create([
            //         'ID_Bahan_Baku' => $ingredient['ID_Bahan_Baku'],
            //         'Kuantitas' => $ingredient['Kuantitas'],
            //         'Tanggal' => Carbon::now(),
            //     ]);
            //     $iniBahanBaku = tblbahanbaku::where('ID_Bahan_Baku', $ingredient['ID_Bahan_Baku'])->first();

            //     tblbahanbaku::where('ID_Bahan_Baku', $ingredient['ID_Bahan_Baku'])
            //         ->first()
            //         ->update(['Stok' => $iniBahanBaku->Stok - $ingredient['Kuantitas']]);
            // }

            tbltransaksi::where('ID_Transaksi', $id)->update(['Status' => 'diproses']);

            return response()->json([
                'message' => 'Status Transaksi berhasil diubah',
                'data' => $transaksi,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Update Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function LaporanPenjualanTahunan($tahun) {
        try {
            $transaksi = DB::table('tbltransaksi as T')
                ->leftJoin(DB::raw('
                (SELECT ID_Transaksi, SUM(Kuantitas) AS Total_Kuantitas
                FROM tbldetailtransaksi
                GROUP BY ID_Transaksi) AS DT
            '), 'T.ID_Transaksi', '=', 'DT.ID_Transaksi')
            ->select(
                DB::raw('extract(month from T.Tanggal_Ambil) as bulan'),
                DB::raw('SUM(T.Total_Pembayaran) as total_pendapatan'),
                DB::raw('COALESCE(SUM(DT.Total_Kuantitas), 0) as total_penjualan')
            )
            ->where('status', 'Selesai')
            ->whereNotNull('T.Tanggal_Ambil')
            ->whereYear('T.Tanggal_Ambil', $tahun)
            ->groupBy(DB::raw('extract(month from T.Tanggal_Ambil)'))
            ->get();

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Laporan untuk tahun ' . $tahun . ' kosong',
                    'data' => null,
                ], 200);
            }

            $laporanTransaksi = ([
                'Tahun' => $tahun,
                'Tanggal_Cetak' => date('Y-m-d'),
                'data' => $transaksi,
                'Total_Penjualan' => $transaksi->sum('total_pendapatan'),
            ]);

            return response()->json([
                'message' => 'Berhasil Mendapatkan Laporan Penjualan Bulanan',
                'data' => $laporanTransaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error On Making Summary Penjualanan Bulanan',
                'data' => $e->getMessage(),
            ], 200);
        }
    }

    public function laporanPenjualanBulanan($bulan, $tahun) {
        try {
            $transaksi = tbltransaksi::whereMonth('Tanggal_Transaksi', $bulan)
                                  ->whereYear('Tanggal_Transaksi', $tahun)
                                  ->where('Status', 'Selesai')
                                  ->with(['tblcustomer', 'products'])
                                  ->get();

            if ($transaksi->count() == 0) {
                return response()->json([
                    'message' => 'Laporan untuk bulan ' . $bulan . ' tahun ' . $tahun . ' kosong',
                    'data' => null,
                ], 200);
            }

            $groupedTransactions = [];
            foreach ($transaksi as $trans) {
                foreach ($trans->products as $product) {
                    if (!isset($groupedTransactions[$product->Nama_Produk])) {
                        $groupedTransactions[$product->Nama_Produk] = [
                            'harga' => $product->Harga,
                            'total_terjual' => 0,
                            'total_pendapatan' => 0,
                            'transaksi' => [],
                        ];
                    }
                    $groupedTransactions[$product->Nama_Produk]['total_terjual'] += $product->pivot->Kuantitas;
                    $groupedTransactions[$product->Nama_Produk]['total_pendapatan'] += $product->pivot->Sub_Total;
                    $groupedTransactions[$product->Nama_Produk]['transaksi'][] = $trans;
                }
            }

            $laporanTransaksi = [
                'Tahun' => $tahun,
                'Bulan' => $bulan,
                'Tanggal_Cetak' => date('Y-m-d'),
                'data' => $groupedTransactions,
                'Total_Penjualan' => $transaksi->sum('Total_Transaksi'),
            ];

            return response()->json([
                'message' => 'Berhasil Mendapatkan Laporan Penjualan Bulanan',
                'data' => $laporanTransaksi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error On Making Summary Penjualanan Bulanan',
                'data' => $e->getMessage(),
            ], 200);
        }
    }
}