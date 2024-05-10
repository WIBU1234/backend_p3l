<?php

namespace App\Http\Controllers;

use App\Models\tblpresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TblpresensiController extends Controller
{
    public function index() {
        try {
            $presensi = tblpresensi::join('tblpegawai', 'tblpresensi.ID_Pegawai', '=', 'tblpegawai.ID_Pegawai')
                ->with('pegawai')
                ->get();

                // join('tblpegawai', 'tblpresensi.ID_Pegawai', '=', 'tblpegawai.ID_Pegawai')
                // ->

            if ($presensi->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data presensi kosong',
                    'data' => null
                ], 404);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Data presensi berhasil diambil',
                    'data' => $presensi
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data presensi gagal diambil',
                'data' => null
            ], 500);
        }
    } 

    public function store(request $request) {
        try {
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'ID_Pegawai' => 'required',
                'Keterangan' => 'required'
            ]);

            if ($validate->fails())
                return response(['message' => $validate->errors()], 400);

            $storeData['Tanggal'] = date('Y-m-d');
            
            $presensi = tblpresensi::create($storeData);

            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil ditambahkan',
                'data' => $presensi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
