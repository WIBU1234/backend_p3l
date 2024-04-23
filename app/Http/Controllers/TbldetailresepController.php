<?php

namespace App\Http\Controllers;

use App\Models\tbldetailresep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TbldetailresepController extends Controller
{
    public function index() {
        $tblDetailResep = tbldetailresep::all();

        if(count($tblDetailResep) == 0) {
            return response()->json([
                'message' => 'Detail Resep Tidak Ditemukan',
                'status' => 404
            ], 404);
        } else {
            return response()->json([
                'message' => 'Detail Resep Berhasil Ditemukan',
                'status' => 200,
                'data' => $tblDetailResep
            ], 200);
        }
    }

    private function validateItem($data)
    {
        return validator($data, [
            'ID_Bahan_Baku' => 'required',
            'ID_Produk' => 'required',
            'Kuantitas' => 'required|numeric|min:0',
        ])->validate();
    }

    public function store(Request $request) {
        $simpanDetailResep = $request->all();
        
        foreach ($simpanDetailResep as $data) {
            $validateData = $this->validateItem($data);

            tbldetailresep::create($validateData);
        }

        $detailResep = tbldetailresep::where('ID_Produk', $simpanDetailResep[0]['ID_Produk'])->get();

        return response()->json([
            'message' => 'Detail Resep Berhasil Disimpan',
            'status' => 200,
            'data' => $detailResep
        ], 200);
    }

    public function show(string $id) {
        $tblDetailResep = tbldetailresep::find($id);

        if(is_null($tblDetailResep)) {
            return response()->json([
                'message' => 'Detail Resep Tidak Ditemukan',
                'status' => 404
            ], 404);
        } else {
            return response()->json([
                'message' => 'Detail Resep Berhasil Ditemukan',
                'status' => 200,
                'data' => $tblDetailResep
            ], 200);
        }
    }

    public function update(request $request, string $id) {
        $simpanDetailResep = $request->all();
        
        foreach ($simpanDetailResep as $data) {
            $validateData = $this->validateItem($data);

            $tblDetailResep = tbldetailresep::where('ID_Produk', $id)->first();
            if (is_null($tblDetailResep)) {
                return response()->json([
                    'message' => 'Detail Resep Tidak Ditemukan',
                    'status' => 404
                ], 404);
            } else {
                $tblDetailResep->update($validateData);
            }
        }



        return response()->json([
            'message' => 'Detail Resep Berhasil Diupdate',
            'status' => 200,
            'data' => $tblDetailResep
        ], 200);
    }

    public function destroy(string $id) {
        $tblDetailResep = tbldetailresep::where('ID_Produk', $id)->get();

        if (is_null($tblDetailResep)) {
            return response()->json([
                'message' => 'Detail Resep Tidak Ditemukan',
                'status' => 404
            ], 404);
        } else {
            $tblDetailResep->delete();
            return response()->json([
                'message' => 'Detail Resep Berhasil Dihapus',
                'status' => 200
            ], 200);
        }
    }

    public function showRelatedProduct(string $namaBahan){
        $result = DB::table('tblbahanbaku as BK')
            ->join('tbldetailresep as DR', 'DR.ID_Bahan_Baku', '=', 'BK.ID_Bahan_Baku')
            ->join('tblresep as R', 'R.ID_Produk', '=', 'DR.ID_Produk')
            ->join('tblproduk as P', 'P.ID_Produk', '=', 'R.ID_Produk')
            ->where('BK.Nama_Bahan', 'like', $namaBahan)
            ->orderBy('P.Nama_Produk')
            ->select('P.Nama_Produk', 'BK.Nama_Bahan', 'DR.Kuantitas', 'BK.Satuan')
            ->get();

        if(count($result) == 0){
            return response()->json([
                'message' => 'Detail Resep Tidak Ditemukan',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Detail Resep Berhasil Ditemukan',
            'status' => 200,
            'data' => $result
        ], 200);
    }
}
