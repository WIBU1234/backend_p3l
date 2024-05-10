<?php

namespace App\Http\Controllers;

use App\Models\tblcustomer;
use App\Models\tblpegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        $registrasiData = $request->all();
        $validate = Validator::make($registrasiData,[
            'Nama_Customer' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:tblCustomer',
            'password' => 'required',
            'Nomor_telepon' => 'required|numeric',
            // 'Profile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $registrasiData['Poin'] = 0;
        $registrasiData['Saldo'] = 0;
        $registrasiData['OTP'] = 0;
        $registrasiData['Password'] = bcrypt($request->Password);

        $tblCustomer = tblcustomer::create($registrasiData);

        return response([
            'message' => 'Registrasi Berhasil, Silahkan Login',
            'user' => $tblCustomer
        ],200);
    }
    //register rachell buat postman
    public function registerCustomer(Request $request){
        $registrationData = $request->all();

        $validate = Validator::make($registrationData, [
            'Nama_Customer' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:tblcustomer',
            'password' => 'required',
            'Nomor_telepon' => 'required',
            // 'Poin',
            // 'Saldo',
            // 'OTP',
            // 'Profile'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        //$registrationData['Password'] = bcrypt($request->Password);
        $registrationData['Poin'] = 0;
        $registrationData['Saldo'] = 0;

        $user = tblcustomer::create($registrationData);

        return response([
            'message' => 'Register Success',
            'user' => $user
        ], 200);
    }


    public function registerPegawai(Request $request){
        $registrationData = $request->all();

        $validate = Validator::make($registrationData, [
            'ID_Jabatan' => 'required',
            'Nama_Pegawai' => 'required|max:60',
            'Nomor_Rekening' => 'required',
            'email' => 'required|email:rfc,dns|unique:tblcustomer',
            'password' => 'required',
            'Nomor_Telepon' => 'required',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        //$registrationData['Password'] = bcrypt($request->Password);
        $registrationData['Gaji'] = 0;
        $registrationData['Bonus'] = 0;

        $user = tblpegawai::create($registrationData);

        return response([
            'message' => 'Register Success',
            'user' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $loginData = $request->all();

        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        if (Auth::guard('customer')->attempt($loginData)) {
            /**  @var \App\Models\tblcustomer $user */
            $user = Auth::guard('customer')->user();
        }elseif (Auth::guard('pegawai')->attempt($loginData)) {
            /**  @var \App\Models\tblpegawai $user */
            $user = Auth::guard('pegawai')->user();
        }else {
            return response([
                'message'=>'Invalid Credential',
                'users' => $loginData
            ],401);
        }
        
        $role = $user->getRole();
        $token = $user->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user'=> $user,
            'role' => $role,
            'token_type' => 'Bearer',
            'access_token'=> $token,
        ]);
    }

    public function logoutCustomer(){
        $user = Auth::user();
        $user->token()->revoke();
        
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function logoutPegawai(){
        $user = Auth::user();
        $user->token()->revoke();
        
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
