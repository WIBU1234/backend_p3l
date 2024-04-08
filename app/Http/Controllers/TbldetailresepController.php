<?php

namespace App\Http\Controllers;

use App\Models\tbldetailresep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TbldetailresepController extends Controller
{
    public function index() {
        $tblDetailResep = tbldetailresep::all();

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

    public function store(array $dataArray) {
        foreach ($dataArray as $data) {
            $simpanDetailResep = $data;
        }

        $validate = Validator::make($simpanDetailResep, [
            'ID_Resep' => 'required',
            'ID_BahanBaku' => 'required',
            'Jumlah' => 'required'
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors(),
                'status' => 404
            ], 404);
        } else {
            $tblDetailResep = tbldetailresep::create($simpanDetailResep);
            return response()->json([
                'message' => 'Detail Resep Berhasil Disimpan',
                'status' => 200,
                'data' => $tblDetailResep
            ], 200);
        }
    }

    public function show(int $id) {
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

    public function update(array $dataArray, int $id) {
        foreach ($dataArray as $data) {
            $updateDetailResep = $data;
        }

        $validate = Validator::make($updateDetailResep, [
            'ID_Resep' => 'required',
            'ID_BahanBaku' => 'required',
            'Jumlah' => 'required'
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors(),
                'status' => 404
            ], 404);
        } else {
            $tblDetailResep = tbldetailresep::find($id);

            if (is_null($tblDetailResep)) {
                return response()->json([
                    'message' => 'Detail Resep Tidak Ditemukan',
                    'status' => 404
                ], 404);
            } else {
                $tblDetailResep->update($updateDetailResep);
                return response()->json([
                    'message' => 'Detail Resep Berhasil Diupdate',
                    'status' => 200,
                    'data' => $tblDetailResep
                ], 200);
            }
        }
    }

    public function destroy(int $id) {
        $tblDetailResep = tbldetailresep::find($id);

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
}
