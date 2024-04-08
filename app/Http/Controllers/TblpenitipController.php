<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
}
