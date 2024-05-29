<?php

namespace App\Http\Controllers;

use App\Models\tblalamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TblalamatController extends Controller
{
    public function getSpesificAddressByIdUser($id)
    {
        $address = tblalamat::where('ID_Customer', $id)->get();

        if (count($address) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $address
            ], 200);
        } else {
            return response([
                'message' => 'Empty',
                'data' => null
            ], 404);
        }
    }

    // Untuk Customer
    public function index() {
        $user = Auth::user();

        $alamat = tblalamat::with('tblcustomer')->where('ID_Customer', $user->ID_Customer)->get();

        if ($alamat->count() == 0) {
            return response()->json([
                'message' => 'Alamat Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Fetch Alamat Success',
            'data' => $alamat,
        ], 200);
    }

    public function store (request $request) {
        try {
            $storeAlamat = $request->all();
            $user = Auth::user();
            $validate = Validator::make($storeAlamat, [
                'Alamat' => 'required',
            ]);

            if ($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                    'status' => 404
                ], 404);
            } else {
                $storeAlamat['ID_Customer'] = $user->ID_Customer;
                $storeAlamat['Jarak'] = 0;
                $storeAlamat['Biaya'] = 0;

                $alamat = tblalamat::create($storeAlamat);

                return response()->json([
                    'message' => 'Data Alamat Berhasil Disimpan',
                    'status' => 200,
                    'data' => $alamat
                ], 200);
            }
        } catch (\Throwable $th) {
            return response([
                'message' => $th->getMessage(),
                'status' => 404
            ], 404);
        }
    }

    public function update ($id) {
        try {
            $user = Auth::user();
            $updateAlamat = request()->all();
            $validate = Validator::make($updateAlamat, [
                'Alamat' => 'required',
            ]);

            if ($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                    'status' => 404
                ], 404);
            } else {
                $updateAlamat['Jarak'] = 0;
                $updateAlamat['Biaya'] = 0;
                $alamat = tblalamat::where('ID_Alamat', $id)
                        ->where('ID_Customer', $user->ID_Customer)
                        ->update($updateAlamat);
                $dataAlamat = tblalamat::where('ID_Alamat', $id)->get();
                return response()->json([
                    'message' => 'Data Alamat Berhasil Diupdate',
                    'status' => 200,
                    'total' => $alamat,
                    'data' => $dataAlamat
                ], 200);
            }
        } catch (\Throwable $th) {
            return response([
                'message' => $th->getMessage(),
                'status' => 404
            ], 404);
        }
    }

    public function destroy ($id) {
        try {
            $user = Auth::user();
            $alamat = tblalamat::where('ID_Alamat', $id)
                    ->where('ID_Customer', $user->ID_Customer)
                    ->delete();

            if ($alamat == 0) {
                return response()->json([
                    'message' => 'Alamat Tidak Ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Data Alamat Berhasil Dihapus',
                'status' => 200,
                'total' => $alamat
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'message' => $th->getMessage(),
                'status' => 404
            ], 404);
        }
    }

    //Untuk Admin
    public function showAllAlamat() {
        try {
            $alamat = tblalamat::join('tblcustomer', 'tblalamat.ID_Customer', '=', 'tblcustomer.ID_Customer')
                    ->with('tblcustomer')
                    ->orderBy('tblcustomer.Nama_Customer')
                    ->get();

            if ($alamat->count() == 0) {
                return response()->json([
                    'message' => 'Tidak Ada Alamat Yang Tersedia',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Fetch Alamat Success',
                'data' => $alamat,
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'message' => $th->getMessage(),
                'status' => 404
            ], 404);
        }
    }

    public function ShowAllAlamatWithNoJarak () {
        try {
            $alamat = tblalamat::join('tblcustomer', 'tblalamat.ID_Customer', '=', 'tblcustomer.ID_Customer')
                    ->join('tbltransaksi', 'tblalamat.ID_Alamat', '=', 'tbltransaksi.ID_Alamat')
                    ->where('tbltransaksi.Status', '=', 'Menunggu Konfirmasi Admin')
                    ->where('tblalamat.Jarak', '=' , 0)
                    ->with('tblcustomer')
                    ->orderBy('tblcustomer.Nama_Customer')
                    ->get();

            if ($alamat->count() == 0) {
                return response()->json([
                    'message' => 'Jarak Alamat Pada Transaksi Sudah Tersedia',
                    'data' => $alamat
                ], 200);
            }

            return response()->json([
                'message' => 'Fetch Alamat Success',
                'data' => $alamat,
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'message' => $th->getMessage(),
                'status' => 404
            ], 404);
        }
    }

    public function showOneAlamat($idC, $idA) {
        try {
            $alamat = tblalamat::join('tblcustomer', 'tblalamat.ID_Customer', '=', 'tblcustomer.ID_Customer')
                    ->join('tbltransaksi', 'tblalamat.ID_Alamat', '=', 'tbltransaksi.ID_Alamat')
                    ->with('tblcustomer')
                    ->where('tblalamat.ID_Customer', $idC)->where('tblalamat.ID_Alamat', $idA)->first();

            if ($alamat == null) {
                return response()->json([
                    'message' => 'Alamat Tidak Ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Fetch Alamat Success',
                'data' => $alamat,
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'message' => $th->getMessage(),
                'status' => 404
            ], 404);
        }
    }

    public function updateJarakBiaya (request $request, $idC, $idA) {
        try {
            $updateJarakBiaya = $request->all();
            $validate = Validator::make($updateJarakBiaya, [
                'Jarak' => 'required',
                'Biaya' => 'required',
            ]);

            if ($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                    'status' => 404
                ], 404);
            } else {
                $alamat = tblalamat::where('ID_Customer', $idC)->where('ID_Alamat', $idA)->update($updateJarakBiaya);
                $dataAlamat = tblalamat::where('ID_Customer', $idC)->where('ID_Alamat', $idA)->first();

                return response()->json([
                    'message' => 'Data Jarak dan Biaya Berhasil Diupdate',
                    'status' => 200,
                    'total' => $alamat,
                    'data' => $dataAlamat
                ], 200);
            }
        } catch (\Throwable $th) {
            return response([
                'message' => $th->getMessage(),
                'status' => 404
            ], 404);
        }
    }
}
