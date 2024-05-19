<?php

namespace App\Http\Controllers;
use App\Models\tblkategori;
use Illuminate\Http\Request;

class TblkategoriController extends Controller
{
    public function getAllKategori(){
        try{
            $kategori = tblkategori::all();

            if($kategori->isEmpty()){
                return response()->json([
                    'message' => 'Data Kategori Kosong',
                    'data' => $kategori,
                ], 200);
            }

            return response()->json([
                'message' => 'Fetch All Kategori Success',
                'data' => $kategori,
            ], 200);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Fetch All Kategori Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }
}
