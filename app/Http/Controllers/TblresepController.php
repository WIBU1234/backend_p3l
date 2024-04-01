<?php

namespace App\Http\Controllers;

use App\Models\tblresep;
use App\Models\tblproduk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class TblresepController extends Controller
{
    public function index() {
        $tblresep = tblresep::with(['tblproduk'])->get();

        if (count($tblresep) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $tblresep
            ], 200);
        } else {
            return response([
                'message' => 'Empty',
                'data' => null
            ], 404);
        }
    }

    public function store(Request $request) {
        $simpanResep = $request->all();

        $validate = Validator::make($simpanResep, [
            'ID_Produk' => 'required',
            'Waktu_Memproses' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $produk = tblproduk::find($simpanResep['ID_Produk']);
        if ($produk == null) {
            return response([
                'message' => 'Produk Not Found'
            ], 404);
        }

        $tblresep = tblresep::create($simpanResep);
        return response([
            'message' => 'Resep Berhasil Disimpan',
            'data' => $tblresep
        ], 200);
    }

    public function update(Request $request, $id) {
        $tblresep = tblresep::find($id);

        if ($tblresep == null) {
            return response([
                'message' => 'Resep Not Found',
                'data' => null
            ], 404);
        }

        $updateResep = $request->all();

        $validate = Validator::make($updateResep, [
            'Waktu_Memproses' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $produk = tblproduk::find($updateResep['ID_Produk']);
        if ($produk == null) {
            return response([
                'message' => 'Produk Not Found'
            ], 404);
        }

        $tblresep->update($updateResep);
        return response([
            'message' => 'Resep Berhasil Diupdate',
            'data' => $tblresep
        ], 200);
    }

    public function destroy($id) {
        $tblresep = tblresep::find($id);

        if ($tblresep == null) {
            return response([
                'message' => 'Resep Not Found',
                'data' => null
            ], 404);
        }

        $tblresep->delete();
        return response([
            'message' => 'Resep Berhasil Dihapus',
            'data' => $tblresep
        ], 200);
    }

    public function show($id) {
        $tblresep = tblresep::with(['tblproduk'])->find($id);

        if ($tblresep == null) {
            return response([
                'message' => 'Resep Not Found',
                'data' => null
            ], 404);
        }

        return response([
            'message' => 'Retrieve Resep Success',
            'data' => $tblresep
        ], 200);
    }
}
