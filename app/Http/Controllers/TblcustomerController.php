<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\tblcustomer;
use App\Models\PasswordReset;
use App\Models\tbltransaksi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TblcustomerController extends Controller
{
    use Notifiable, CanResetPassword;

    public function forgetPassword(Request $request){
        try{
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = tblcustomer::where('email', $request->email)->get();

            if ($user->isEmpty()) {
                return response()->json([
                    'message' => 'Gk ada emailnya',
                ], 404);
            }

            $resetCreated = PasswordReset::where('email', $request->email)->delete();

            $token = Str::random(40);
            $domain = URL::to('http://localhost:5173');
            $url = $domain . '/reset-password?token='.$token;

            $data['url'] = $url;
            $data['email'] = $request->email;
            $data['title'] = 'Reset Password';
            $data['body'] = 'Silahkan klik link dibawah ini untuk mereset password anda';

            Mail::send('forgotPasswordMail', ['data'=>$data], function($message) use ($data){
                $message->to($data['email'])->subject($data['title']);
            });

            $datetime = Carbon::now()->format('Y-m-d H:i:s');
            PasswordReset::updateOrCreate(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => $datetime
                ]
            );

            return response()->json([
                'message' => 'Silahkan cek email anda untuk mereset password'
            ], 200);

        }
        catch(\Exception $e){
            Log::error('Error in forgetPassword:', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'Gagal',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function checkingCredentialToken(Request $request){
        try{
            $request->validate([
                'token' => 'required'
            ]);

            $user = PasswordReset::where('token', $request->token)->get();

            if ($user->isEmpty()) {
                return response()->json([
                    'message' => 'Token invalid',
                    'data' => 'Token invalid'
                ], 404);
            }

            return response()->json([
                'message' => 'Token valid',
                'data' => 'Token valid'
            ], 200);

        }
        catch(\Exception $e){
            Log::error('Error in checkingCredentialToken:', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'Gagal',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function updatePoin(Request $request) {
        $user = Auth::user();
        $storedData = $request->all();
        try {
            $validate = Validator::make($storedData, [
                'Poin' => 'required'
            ]);

            if($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                    'status' => 404
                ], 404);
            } 

            $user->Poin = $storedData['Poin'];
            
            if ($user->save()) {
                return response([
                    'message' => 'Store Transaksi Success',
                    'data' => $user,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Store Transaksi Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function resetPassword(Request $request){
        try{
            $request->validate([
                'token' => 'required',
                'password' => 'required',
                'passwordConfirm' => 'required|same:password'
            ]);

            $user = PasswordReset::where('token', $request->token)->get();

            if ($user->isEmpty()) {
                return response()->json([
                    'message' => 'Token invalid',
                    'data' => 'Token invalid'
                ], 404);
            }

            $user = tblcustomer::where('email', $user[0]->email)->get();

            if ($user->isEmpty()) {
                return response()->json([
                    'message' => 'Gk ada emailnya',
                ], 404);
            }

            $user[0]->password = bcrypt($request->password);
            $user[0]->save();

            return response()->json([
                'message' => 'Password berhasil direset'
            ], 200);
            
        }
        catch(\Exception $e){
            Log::error('Error in resetPassword:', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'Gagal',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function confirmEmail(Request $request) {
        $user = tblcustomer::where('email', $request->email)->get();

        if ($user->isEmpty()) {
            return response()->json([
                'message' => 'User Not Found',
            ], 404);
        }

        $domain = URL::to('http://localhost:5173');
        $url = $domain . '/';

        $data['url'] = $url;
        $data['email'] = $request->email;
        $data['title'] = 'Konfirmasi Email';

        Mail::send('confirmationEmailRegister', ['data'=>$data], function($message) use ($data){
            $message->to($data['email'])->subject($data['title']);
        });

        return response()->json([
            'message' => 'Berhasil Mengirimkan Email Konfirmasi'
        ], 200);
    }

    public function index() {
        $user = Auth::user();
        $tblcustomer = tblcustomer::find($user->ID_Customer)->with(['tblalamat'])->get();
        if (!$user) {
            return response()->json([
                'message' => 'User Not Found',
            ], 404);
        } else {
            return response()->json([
                'message' => 'User Found',
                'data' => $tblcustomer
            ], 200);
        }
    }

    public function getAlamatUser() {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'User Not Found',
            ], 404);
        }
    
        // Fetch the user's address
        $alamat = $user->tblalamat;
    
        return response()->json([
            'message' => 'User Found',
            'data' => $alamat,
        ], 200);
    }

    public function updateProfile(Request $request) {
        $user = Auth::user();
        $tblcustomer = tblcustomer::find($user->ID_Customer);
    
        if ($tblcustomer == null) {
            return response()->json([
                'message' => 'User Not Found',
            ], 404);
        }
        
        $updateProfile = $request->all();

        $validate = Validator::make($updateProfile, [
            'Profile' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors(),
            ], 400);
        }
    
        if ($request->hasFile('Profile')) {
            // $uploadFolder = 'customer';
            // $image = $request->file('Profile');
            // $image_uploaded_path = $image->store($uploadFolder, 'public');
            // $uploadImageResponse = basename($image_uploaded_path);
    
            // Storage::disk('public')->delete('customer/'.$tblcustomer->Profile);
    
            // $updateProfile['Profile'] = $uploadImageResponse;
            $image = $request->file('Profile');
            $originalName = $image->getClientOriginalName();
            
            if($tblcustomer->Profile !== null){
                $cloudinaryController = new cloudinaryController();
                $cloudinaryController->deleteImageFromCloudinary($tblcustomer->Profile);
            }

            $cloudinaryController = new cloudinaryController();
            $public_id = $cloudinaryController->sendImageToCloudinary($image, $originalName);
            $updateProfile['Profile'] = $public_id;
            $tblcustomer->update($updateProfile);
        } else {
            return response ([
                'message' => 'No such File included',
            ], 401);
        }
    
        return response([
            'message' => 'Profile image updated successfully',
            'data' => $tblcustomer,
        ], 200);
    }    

    public function update(request $request) {
        try {
            $user = Auth::user();
            $tblcustomer = tblcustomer::find($user->ID_Customer);
            $updatecustomer = $request->all();
            $validate = Validator::make($updatecustomer, [
                'Nama_Customer' => 'required',
                'email' => 'required',
                'Nomor_telepon' => 'required',
            ]);

            if ($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                ], 404);
            }

            $tblcustomer->update($updatecustomer);

            return response()->json([
                'message' => 'Berhasil Mengupdate User ' . $tblcustomer->Nama_Customer,
                'data' => $tblcustomer
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User Not Found',
            ], 404);
        }
    }

    public function getAllCustomer(){
        try{
            $customer = tblcustomer::all();

            return response()->json([
                'message' => 'Get All Customer Success',
                'data' => $customer,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Get All Customer Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function searchGetCustomer(Request $request){
        try{
            $request->validate([
                'search' => 'required',
            ]);

            $customer = tblcustomer::where('Nama_Customer', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')
                ->get();

            return response()->json([
                'message' => 'Search Customer Success',
                'data' => $customer,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Search Customer Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function getCustomerHistory($id){
        try{
            $customer = tblcustomer::find($id);

            if(!$customer){
                return response()->json([
                    'message' => 'Customer Not Found',
                    'data' => '404',
                ], 404);
            }

            $history = $customer->with(['tbltransaksi' => function ($query) {
                $query->where('Status', 'Selesai')
                ->with('tbldetailtransaksi.tblproduk');
            }])
                ->where('ID_Customer', $id)
                ->first();

            if(is_null($history)){
                return response()->json([
                    'message' => 'Customer History Not Found',
                    'data' => '404',
                ], 404);
            }

            return response()->json([
                'message' => 'Get Customer History Success',
                'data' => $history,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Get Customer History Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function showAllNeedToPay(){
        try{
            if (!Auth::check()) {
                return response()->json([
                    'message' => 'Authentication Failed',
                    'data' => '401',
                ], 401);
            }

            $user = Auth::user();
            $customer = tblcustomer::find($user->ID_Customer)->first();

            if($customer == null){
                return response()->json([
                    'message' => 'Customer Not Found',
                    'data' => '404',
                ], 404);
            }

            $transaksi = tbltransaksi::where('ID_Customer', $user->ID_Customer)
                ->where('Status', 'belum dibayar')
                ->get();

            if($transaksi->isEmpty()){
                return response()->json([
                    'message' => 'There is no need items to pay',
                    'data' => '404',
                ], 404);
            }

            return response()->json([
                'message' => 'Get All Need To Pay Success',
                'data' => $transaksi,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Get All Need To Pay Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function sendImageForPaying(Request $request){
        try{
            $request->validate([
                'ID_Transaksi' => 'required',
                'Bukti_Pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',                
            ]);

            $uploadFolder = 'img';
            $gambarProduk = $request->file('Bukti_Pembayaran');
            $gambarProdukFiles = $gambarProduk->store($uploadFolder, 'public');
            $gambarProdukPath = basename($gambarProdukFiles);
            
            $user = Auth::user();
            // $customer = tblcustomer::find($user->ID_Customer)->first();
            $customer = tblcustomer::where('ID_Customer', $user->ID_Customer)->first();

            if($customer == null){
                return response()->json([
                    'message' => 'Customer Not Found',
                    'data' => '404',
                ], 404);
            }

            $transaksi = tbltransaksi::where('ID_Transaksi', $request->ID_Transaksi)
                ->where('ID_Customer', $customer->ID_Customer)
                ->first();
            
            if($transaksi == null){
                return response()->json([
                    'message' => 'Transaction Not Found',
                    'data' => '404',
                ], 404);
            }

            // Testing cloudinary
            $cloudinaryImage = $request->file('Bukti_Pembayaran')->storeOnCloudinary('test');
            $url = $cloudinaryImage->getSecurePath();
            $public_id = $cloudinaryImage->getPublicId();

            tbltransaksi::where('ID_Transaksi', $request->ID_Transaksi)->update(['Bukti_Pembayaran' => $public_id]);
            $transaksi = tbltransaksi::where('ID_Transaksi', $request->ID_Transaksi)
                ->where('ID_Customer', $customer->ID_Customer)
                ->first();

            return response()->json([
                'message' => 'Send Image For Paying Success',
                'data' => $transaksi,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Send Image For Paying Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function testUpload(Request $request){
        try{
            if($request->hasFile('image')) {
                $image = $request->file('image');
                $originalName = $image->getClientOriginalName();
    
                $cloudinaryController = new cloudinaryController();
                $public_id = $cloudinaryController->sendImageToCloudinary($image, $originalName);

                return response()->json([
                    'message' => 'Upload Image Success',
                    'data' => $public_id,
                ], 200);
            }else{
                return response()->json([
                    'message' => 'No such File included',
                ], 401);
            }
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Upload Image Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function testDelete(Request $request){
        try{
            $request->validate([
                'public_id' => 'required',
            ]);

            $cloudinaryController = new cloudinaryController();
            $response = $cloudinaryController->deleteImageFromCloudinary($request->public_id);

            if($response['result'] == 'not found'){
                return response()->json([
                    'message' => 'Image Not Found',
                    'data' => '404',
                ], 404);
            }

            return response()->json([
                'message' => 'Delete Image Success',
                'data' => $response,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Delete Image Failed',
                'data' => $e->getMessage(),
            ], 400);
        }
    }
}
