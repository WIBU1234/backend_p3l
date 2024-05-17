<?php

namespace App\Http\Controllers;

use App\Models\tblalamat;
use App\Models\tblpegawai;
use App\Models\tbltransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TbltransaksiController extends Controller
{
    public function index()
    {
        $transaksi = tbltransaksi::with(['tblalamat', 'tblcustomer', 'products'])
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
                'ID_Alamat' => 'required',
                'Tanggal_Ambil' => 'required',
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
            $storeTransaksi['Status'] = 'Menunggu Pembayaran';
            $storeTransaksi['Tanggal_Transaksi'] = date('Y-m-d H:i:s');
            $storeTransaksi['Total_Pembayaran'] = 0;

            $transaksi = tbltransaksi::create($storeTransaksi);
            $totalHarga = 0;

            if($request->has('products')) {
                $products = $request->input('products');

                $productsData = [];
                foreach ($products as $data) {
                    $productsData[$data['ID_Produk']] = [
                        'Kuantitas' => $data['Kuantitas'],
                        'Sub_Total' => $data['Sub_Total'] //Butuh fungsi autogenerate hitung sub_total
                    ];

                    $totalHarga += $data['Sub_Total'];
                }

                $transaksi->products()->attach($productsData);
            }

            //Di Front End bakal ada field hidden buat nampung total potongan harga

            $transaksi->Total_Transaksi = $totalHarga; //Total Harga mainin di bandend dan frontend

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
}
