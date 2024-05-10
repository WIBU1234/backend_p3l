<?php

namespace App\Http\Controllers;

use App\Models\tblpengeluaran;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TblpengeluaranController extends Controller
{
    public function getAllDataPengeluaran(){
        try{
            $pengeluaran = tblpengeluaran::all();
            return response()->json([
                'message' => 'Fetch All Pengeluaran Success',
                'data' => $pengeluaran,
            ], 200);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch All Pengeluaran Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function createPengeluaran(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'Nama' => 'required|string',
                'Harga' => 'required|integer',
                'Tanggal' => 'required|date',
            ]);

            if($validator->fails()){
                return response()->json([
                    'message' => 'Validasi gagal',
                    'data' => $validator->errors()
                ], 400);
            };

            $pengeluaran = tblpengeluaran::create([
                'Nama' => $request->Nama,
                'Harga' => $request->Harga,
                'Tanggal' => $request->Tanggal,
            ]);

            return response()->json([
                'message' => 'Create Pengeluaran Success',
                'data' => $pengeluaran,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Create Pengeluaran Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function updatePengeluaranByID(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'Nama' => 'required|string',
                'Harga' => 'required|integer',
                'Tanggal' => 'required|date',
            ]);

            if($validator->fails()){
                return response()->json([
                    'message' => 'Validasi gagal',
                    'data' => $validator->errors()
                ], 400);
            };

            $updated = tblpengeluaran::where('ID_Pengeluaran', $id)
                ->update([
                    'Nama' => $request->Nama,
                    'Harga' => $request->Harga,
                    'Tanggal' => $request->Tanggal,
                ]);

            if(!$updated){
                return response()->json([
                    'message' => 'Pengeluaran Not Found',
                    'data' => '404'
                ], 404);
            }

            $data = [
                'Nama' => $request->Nama,
                'Harga' => $request->Harga,
                'Tanggal' => $request->Tanggal,
            ];

            return response()->json([
                'message' => 'Update Pengeluaran Success',
                'data' => $data,
            ], 200);
            
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Update Pengeluaran Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function deletePengeluaranById($id){
        try{
            $deletedData = tblpengeluaran::where('ID_Pengeluaran', $id)->first();

            $deleted = tblpengeluaran::where('ID_Pengeluaran', $id)->delete();
    
            if(!$deleted){
                return response()->json([
                    'message' => 'Pengeluaran Not Found',
                    'data' => '404'
                ], 404);
            }
    
            return response()->json([
                'message' => 'Delete Pengeluaran Success',
                'data' => $deletedData,
            ], 200);
            
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Delete Pengeluaran Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function updatePengeluaran(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'Nama' => 'required|string',
                'Harga' => 'required|integer',
                'Tanggal' => 'required|date',

                'Nama_Old' => 'required|string',
                'Harga_Old' => 'required|integer',
                'Tanggal_Old' => 'required|date',
            ]);

            if($validator->fails()){
                return response()->json([
                    'message' => 'Validasi gagal',
                    'data' => $validator->errors()
                ], 400);
            };

            $updated = tblpengeluaran::where('Nama', $request->Nama_Old)
                ->where('Harga', $request->Harga_Old)
                ->where('Tanggal', $request->Tanggal_Old)
                ->update([
                    'Nama' => $request->Nama,
                    'Harga' => $request->Harga,
                    'Tanggal' => $request->Tanggal,
                ]);

            if(!$updated){
                return response()->json([
                    'message' => 'Pengeluaran Not Found',
                    'data' => '404'
                ], 404);
            }

            $data = [
                'Nama' => $request->Nama,
                'Harga' => $request->Harga,
                'Tanggal' => $request->Tanggal,
            ];

            return response()->json([
                'message' => 'Update Pengeluaran Success',
                'data' => $data,
            ], 200);
            
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Update Pengeluaran Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function deletePengeluaran(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'Nama' => 'required|string',
                'Harga' => 'required|integer',
                'Tanggal' => 'required|date',
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'message' => 'Validasi gagal',
                    'data' => $validator->errors()
                ], 400);
            };
    
            $data = [
                'Nama' => $request->Nama,
                'Harga' => $request->Harga,
                'Tanggal' => $request->Tanggal,
            ];

            $deleted = tblpengeluaran::where('Nama', $request->Nama)
                ->where('Harga', $request->Harga)
                ->where('Tanggal', $request->Tanggal)
                ->delete();
    
            if(!$deleted){
                return response()->json([
                    'message' => 'Pengeluaran Not Found',
                    'data' => '404'
                ], 404);
            }
    
            return response()->json([
                'message' => 'Delete Pengeluaran Success',
                'deleted-data' => $data,
            ], 200);
            
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Delete Pengeluaran Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function searchPengeluaran(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'Nama' => 'required|string',
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'message' => 'Validasi gagal',
                    'data' => $validator->errors()
                ], 400);
            };
    
            $data = tblpengeluaran::where('Nama', 'like', '%'.$request->Nama.'%')->get();
    
            if($data->isEmpty()){
                return response()->json([
                    'message' => 'Pengeluaran Not Found',
                    'data' => '404'
                ], 404);
            }
    
            return response()->json([
                'message' => 'Search Pengeluaran Success',
                'data' => $data,
            ], 200);
            
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Search Pengeluaran Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }
}
