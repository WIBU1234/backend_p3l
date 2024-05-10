<?php

namespace App\Http\Controllers;

use App\Models\tbldetailtransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TbldetailtransaksiController extends Controller
{
    public function store (request $request) {
        try {
            $storeDetailTransaksi = $request->all();

            $validate = Validator::make($storeDetailTransaksi, [
                'ID_Transaksi' => 'required',
                'ID_Produk' => 'required',
                'Kuantitas' => 'required',
                'Sub_Total' => 'required',
            ]);

            if ($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                    'status' => 404
                ], 404);
            } else {
                $detailTransaksi = tbldetailtransaksi::create($storeDetailTransaksi);
                return response([
                    'message' => 'Detail Transaksi Berhasil Ditambahkan',
                    'data' => $detailTransaksi,
                    'status' => 200
                ], 200);
            }


        } catch (\Exception $e) {
            return response([
                'message' => 'Error ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
