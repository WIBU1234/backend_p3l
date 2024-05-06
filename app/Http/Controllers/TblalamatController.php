<?php

namespace App\Http\Controllers;

use App\Models\tblalamat;
use Illuminate\Http\Request;

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
}
