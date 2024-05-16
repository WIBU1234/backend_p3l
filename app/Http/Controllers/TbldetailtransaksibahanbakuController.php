<?php

namespace App\Http\Controllers;

use App\Models\tbldetailtransaksibahanbaku;
use Illuminate\Http\Request;

class TbldetailtransaksibahanbakuController extends Controller
{
    //
    public function index () 
    {
        $produk = tbldetailtransaksibahanbaku::all();

        if(count($produk) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $produk
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }
}
