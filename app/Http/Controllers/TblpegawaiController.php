<?php

namespace App\Http\Controllers;

use App\Models\tblpegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Integer;

class TblpegawaiController extends Controller
{
    public function index() {
        $tblPegawai = tblpegawai::with('jabatan')->get();

        if (count($tblPegawai) == 0) {
            return response()->json([
                'message' => 'Data Pegawai Tidak Ditemukan',
                'status' => 404
            ], 404);
        } else {
            return response()->json([
                'message' => 'Data Pegawai Berhasil Ditemukan',
                'status' => 200,
                'data' => $tblPegawai
            ], 200);
        }
    }

    public function store(Request $request) {
        $simpanPegawai = $request->all();

        $validate = Validator::make($simpanPegawai, [
            'ID_Jabatan' => 'required',
            'Nama_Pegawai' => 'required',
            'Nomor_Rekening' => 'required',
            'email' => 'required',
            'password' => 'required',
            'Nomor_Telepon' => 'required'
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors(),
                'status' => 404
            ], 404);
        } else {
            $simpanPegawai['password'] = bcrypt($request->password);
            $simpanPegawai['gaji'] = 0;
            $simpanPegawai['bonus'] = 0;
            $simpanPegawai['OTP'] = rand(1000, 9999);

            $tblPegawai = tblpegawai::create($simpanPegawai);
            return response()->json([
                'message' => 'Data Pegawai Berhasil Disimpan',
                'status' => 200,
                'data' => $tblPegawai
            ], 200);
        }
    }

    public function show(String $nama) {
        $tblPegawai = tblpegawai::where('Nama_Pegawai', $nama)->first();

        if (is_null($tblPegawai)) {
            return response()->json([
                'message' => 'Data Pegawai Tidak Ditemukan',
                'status' => 404
            ], 404);
        } else {
            return response()->json([
                'message' => 'Data Pegawai Berhasil Ditemukan',
                'status' => 200,
                'data' => $tblPegawai
            ], 200);
        }
    }

    public function update (request $request, $id) {
        $tblPegawai = tblpegawai::find($id);
        if(is_null($tblPegawai)) {
            return response()->json([
                'message' => 'Data Pegawai Tidak Ditemukan',
                'status' => 404
            ], 404);
        }
        
        $updatePegawai = $request->all();
        $validate = Validator::make($updatePegawai, [
            'ID_Jabatan' => 'required',
            'Nama_Pegawai' => 'required',
            'Nomor_Rekening' => 'required',
            'email' => 'required',
            'password' => 'required',
            'Nomor_Telepon' => 'required'
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors(),
                'status' => 404
            ], 404);
        } 

        $tblPegawai->update($updatePegawai);
        return response()->json([
            'message' => 'Data Pegawai Berhasil Diupdate',
            'status' => 200,
            'data' => $tblPegawai
        ], 200);
        
    }

    public function delete($id) {
        $tblPegawai = tblpegawai::find($id);

        if (is_null($tblPegawai)) {
            return response()->json([
                'message' => 'Data Pegawai Tidak Ditemukan',
                'status' => 404
            ], 404);
        } else {
            $tblPegawai->delete();
            return response()->json([
                'message' => 'Data Pegawai Berhasil Dihapus',
                'status' => 200
            ], 200);
        }
    }

    public function updateGaji (Request $request, $id) {
        $tblPegawai = tblpegawai::find($id);

        $updateGaji = request()->validate([
            'Gaji' => 'required'
        ]);

        if (is_null($tblPegawai)) {
            return response()->json([
                'message' => 'Data Pegawai Tidak Ditemukan',
                'status' => 404
            ], 404);
        } else {
            $tblPegawai->Gaji = $updateGaji['Gaji'];
            $tblPegawai->save();
            return response()->json([
                'message' => 'Data Gaji Pegawai Berhasil Diupdate',
                'status' => 200,
                'data' => $tblPegawai
            ], 200);
        }
    }

    public function updateBonus (Request $request, $id) {
        $tblPegawai = tblpegawai::find($id);

        $updateBonus = request()->validate([
            'Bonus' => 'required'
        ]);

        if (is_null($tblPegawai)) {
            return response()->json([
                'message' => 'Data Pegawai Tidak Ditemukan',
                'status' => 404
            ], 404);
        } else {
            $tblPegawai->Bonus = $updateBonus['Bonus'];
            $tblPegawai->save();
            return response()->json([
                'message' => 'Data Bonus Pegawai Berhasil Diupdate',
                'status' => 200,
                'data' => $tblPegawai
            ], 200);
        }
    }
}
