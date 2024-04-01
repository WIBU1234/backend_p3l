<?php

namespace App\Http\Controllers;

use App\Models\tblcustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        $registrasiData = $request->all();
        $validate = Validator::make($registrasiData,[
            'Nama_Customer' => 'required|max:60',
            'Email' => 'required|email:rfc,dns|unique:users',
            'Password' => 'required',
            'Nomor_Telepon' => 'required|numeric',
            'Profile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $registrasiData['Poin'] = 0;
        $registrasiData['Saldo'] = 0;
        $registrasiData['Password'] = bcrypt($request->Password);

        $tblCustomer = tblcustomer::create($registrasiData);

        return response([
            'message' => 'Registrasi Berhasil, Silahkan Login',
            'user' => $tblCustomer
        ],200);
    }
}
