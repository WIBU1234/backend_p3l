<?php

namespace App\Http\Controllers;

use App\Models\tbltitipan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TbltitipanController extends Controller
{
    public function index() {
        $titipan = tbltitipan::with(['tblproduk', 'penitip'])->get();

        if (count($titipan) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $titipan
            ], 200);
        } else {
            return response([
                'message' => 'Empty',
                'data' => null
            ], 404);
        }
    }

    public function store(Request $request) {
        $storedData = $request->all();

        $validate = Validator::make($storedData, [
            'ID_Produk' => 'required',
            'ID_Penitip' => 'required',
            'Harga_Beli' => 'required',
            'Tanggal_Stok' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $titipan = tbltitipan::find($storedData['ID_Produk']);
        if ($titipan == null) {
            return response([
                'message' => 'Produk Not Found'
            ], 404);
        }

        $tbltitipan = tbltitipan::create($storedData);
        return response([
            'message' => 'Resep Berhasil Disimpan',
            'data' => $tbltitipan
        ], 200);
    }

    public function update(Request $request, $id) {
        $tbltitipan = tbltitipan::find($id);

        if ($tbltitipan == null) {
            return response([
                'message' => 'Hampers Not Found',
                'data' => null
            ], 404);
        }

        $updatedTitipan = $request->all();

        $validate = Validator::make($updatedTitipan, [
            'ID_Penitip' => 'required',
            'Harga_Beli' => 'required',
            'Tanggal_Stok' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $titipan = tbltitipan::find($updatedTitipan['ID_Produk']);
        if ($titipan == null) {
            return response([
                'message' => 'Hampers Not Found'
            ], 404);
        }

        $tbltitipan->update($updatedTitipan);
        return response([
            'message' => 'Resep Berhasil Diupdate',
            'data' => $tbltitipan
        ], 200);
    }

    public function destroy($id) {
        $titipan = tbltitipan::find($id);

        if ($titipan == null) {
            return response([
                'message' => 'Titipan Not Found',
                'data' => null
            ], 404);
        }

        $titipan->delete();
        return response([
            'message' => 'Resep Berhasil Dihapus',
            'data' => $titipan
        ], 200);
    }

    public function show($id) {
        $titipan = tbltitipan::with(['tblproduk'])->find($id);

        if ($titipan == null) {
            return response([
                'message' => 'Titipan Not Found',
                'data' => null
            ], 404);
        }

        return response([
            'message' => 'Retrieve Titipan Success',
            'data' => $titipan
        ], 200);
    }
}
