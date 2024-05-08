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

    public function index() {
        $user = Auth::user();

        $alamat = tblalamat::where('ID_Customer', $user->ID_Customer)->get();

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
}
