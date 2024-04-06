<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\tblbahanbaku;

class TblbahanbakuController extends Controller
{
    public function index(){
        try{
            $bahanbaku = tblbahanbaku::all();
            return response()->json([
                'message' => 'Fetch All Acara Success',
                'data' => $bahanbaku,
            ], 200);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch All Acara Failed',
                'data' => $e->getMessage(),
            ], 400);
        }        
    }

    public function createBahanBaku(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'Nama_Bahan' => 'required',
                'Stok' => 'required',
                'Satuan' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'message' => 'Validasi gagal',
                    'data' => $validator->errors()
                ], 400);
            };

            $bahanbaku = tblbahanbaku::create([
                'Nama_Bahan' => $request->Nama_Bahan,
                'Stok' => $request->Stok,
                'Satuan' => $request->Satuan,
            ]);
            
            return response()->json([
                'message' => 'Create Bahan Baku Success',
                'data' => $bahanbaku,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Create Bahan Baku Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function updateBahanBaku(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'Nama_Bahan' => 'required',
                'Stok' => 'required',
                'Satuan' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'message' => 'Validasi gagal',
                    'data' => $validator->errors()
                ], 400);
            };

            $bahanbaku = tblbahanbaku::find($id);

            if(!$bahanbaku){
                return response()->json([
                    'message' => 'Bahan Baku Not Found',
                    'data' => null,
                ], 404);
            }

            $bahanbaku->Nama_Bahan = $request->Nama_Bahan;
            $bahanbaku->Stok = $request->Stok;
            $bahanbaku->Satuan = $request->Satuan;
            $bahanbaku->save();
            
            return response()->json([
                'message' => 'Update Bahan Baku Success',
                'data' => $bahanbaku,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Update Bahan Baku Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }
}
