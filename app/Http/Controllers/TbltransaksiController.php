<?php

namespace App\Http\Controllers;

use App\Models\tbltransaksi;
use Illuminate\Http\Request;

class TbltransaksiController extends Controller
{
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
}
