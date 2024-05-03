<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\tblpenitip;

class TblpenitipController extends Controller
{
    public function index(){
        try{
            $penitip = tblpenitip::all();
            return response()->json([
                'message' => 'Fetch All Penitip Success',
                'data' => $penitip,
            ], 200);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch All Penitip Failed',
                'data' => $e->getMessage(),
            ], 400);
        }        
    }

    public function createPenitip(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'Nama_Penitip' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'message' => 'Validasi gagal',
                    'data' => $validator->errors()
                ], 400);
            };

            $penitip = tblpenitip::create([
                'Nama_Penitip' => $request->Nama_Penitip,
            ]);
            
            return response()->json([
                'message' => 'Create Penitip Success',
                'data' => $penitip,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Create Penitip Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function updatePenitip(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'Nama_Penitip' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'message' => 'Validasi gagal',
                    'data' => $validator->errors()
                ], 400);
            };

            $penitip = tblpenitip::where('ID_Penitip', $id)->first();

            if(!$penitip){
                return response()->json([
                    'message' => 'Penitip not found',
                ], 404);
            }

            $penitip->update([
                'Nama_Penitip' => $request->Nama_Penitip,
            ]);
            
            return response()->json([
                'message' => 'Update Penitip Success',
                'data' => $penitip,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Update Penitip Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function deletePenitip($id){
        try{
            $penitip = tblpenitip::where('ID_Penitip', $id)->first();

            if(!$penitip){
                return response()->json([
                    'message' => 'Penitip not found',
                ], 404);
            }

            $penitip->delete();
            
            return response()->json([
                'message' => 'Delete Penitip Success',
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Delete Penitip Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function getAllProductByPenitip(Request $request){
        try{
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'ID_Penitip' => 'required'
            ]);
    
            if($validate->fails()){
                return response(['message' => $validate->errors()], 400);
            }

            $produk = DB::table('tblpenitip as P')
                ->join('tbltitipan as T', 'P.ID_Penitip', '=', 'T.ID_Penitip')
                ->join('tblproduk as PR', 'T.ID_Produk', '=', 'PR.ID_Produk')
                ->where('P.ID_Penitip', $storeData['ID_Penitip'])
                ->select('PR.Nama_Produk', 'T.Harga_Beli', 'PR.Harga', 'PR.Stok')
                ->get();
            
            return response()->json([
                'message' => 'Fetch Produk by Penitip Success',
                'data' => $produk,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch Produk by Penitip Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }
}
