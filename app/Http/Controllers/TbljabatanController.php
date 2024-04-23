<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tbljabatan;

class TbljabatanController extends Controller
{
    public function index() {
        $tblJabatan = tbljabatan::all();

        if (count($tblJabatan) == 0) {
            return response()->json([
                'message' => 'Data Jabatan Tidak Ditemukan',
                'status' => 404
            ], 404);
        } else {
            return response()->json([
                'message' => 'Data Jabatan Berhasil Ditemukan',
                'status' => 200,
                'data' => $tblJabatan
            ], 200);
        }
    }
}
