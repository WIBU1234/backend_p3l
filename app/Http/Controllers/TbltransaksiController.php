<?php

namespace App\Http\Controllers;

use App\Models\tblalamat;
use App\Models\tblpegawai;
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

    


    //Pemesanan
    //Generate ID Transaksi
    //Status auto Menunggu Pembayaran dan Total Bayar 0
    //Nambah poin customer
    //Ngurangin total transaksi klo customer pake poin

    public function store(request $request) {
        try {
            $user = Auth::user();

            $storeTransaksi = $request->all();

            //Dapetin Pegawai Admin
            $pegawai = tblpegawai::where('ID_Jabatan', 2)->first();
            
            $validate = Validator::make($storeTransaksi, [
                'products' => 'required',
                'Total_Transaksi' => 'required',
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
            $storeTransaksi['ID_Alamat'] = 11;
            $storeTransaksi['Status'] = 'Menunggu Pembayaran';
            $storeTransaksi['Tanggal_Transaksi'] = date('Y-m-d H:i:s');
            $storeTransaksi['Total_Pembayaran'] = 0;

            $transaksi = tbltransaksi::create($storeTransaksi);

            if($request->has('products')) {
                $products = $request->input('products');

                $productsData = [];
                foreach ($products as $data) {
                    $productsData[$data['ID_Produk']] = [
                        'Kuantitas' => $data['Kuantitas'],
                        'Sub_Total' => $data['Sub_Total'] //Butuh fungsi autogenerate hitung sub_total
                    ];
                }

                $transaksi->products()->attach($productsData);
            }

            if ($transaksi->save()) {
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

    private function countPoin(String $id_trans) //Buat MO
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

    public function reducePoin(Request $request)
    {
        $storedData = $request->all();

        $user = Auth::user();

        $validate = Validator::make($storedData, [
            'Poin' => 'required|integer'
        ]);

        if ($user->Poin < $storedData['Poin']) {
            return response([
                'message' => 'Poin Kurang'
            ], 400);
        }

        $user->Poin -= $storedData['Poin'];
        $user->save();

        if ($user->save()) {
            return response([
                'message' => 'Store Poin Success',
                'data' => $user,
            ], 200);
        }

        return response([
            'message' => 'Update Content Failed',
            'data' => null 
        ], 400);
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

            if ($transaksi->Status != 'pembayaran valid') {
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
    
    private function calculatePoints($total_transaksi, $transactionDate, $birthday) {
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
    
                // Mengumpulkan bahan-bahan dari tblresep
                if (isset($tblproduk['tblresep'])) {
                    $ingredients = $this->collectIngredientsFromRecipe($tblproduk['tblresep']['tbldetailresep'], $ingredients);
                }
    
                // Mengumpulkan bahan-bahan dari hampers
                if (isset($tblproduk['tblhampers'])) {
                    foreach ($tblproduk['tblhampers']['tbldetailhampers'] as $detailHampers) {
                        if (isset($detailHampers['tblresep'])) {
                            $ingredients = $this->collectIngredientsFromRecipe($detailHampers['tblresep']['tbldetailresep'], $ingredients);
                        }
                    }
                }
            }
        }
        return $ingredients;
    }
    
    private function collectIngredientsFromRecipe($recipeDetails, $ingredients) {
        foreach ($recipeDetails as $resep) {
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
        return $ingredients;
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

            tbltransaksi::where('ID_Transaksi', $id)->update(['Status' => 'ditolak']);
            $customer->Saldo += $transaksi->Total_pembayaran;
            $customer->save();
            $transactionDetails = $transaksi->tbldetailtransaksi;

            foreach ($transactionDetails as $detail) {
                $product = tblproduk::where('ID_Produk', $detail->ID_Produk)->first();

                if ($product != null) {
                    $product->Stok += $detail->Kuantitas;
                    $product->save();
                }
            }

            return response()->json([
                'message' => 'Transaksi berhasil ditolak',
                'data' => $transaksi,
            ]);

            // $transaksi->delete();

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Update Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }
}
