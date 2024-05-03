<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\tblcustomer;
use App\Models\tblhampers;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

    public function getCustomerHistory($id){
        try{
            $customer = tblcustomer::find($id);

            if(!$customer){
                return response()->json([
                    'message' => 'Customer Not Found',
                    'data' => '404',
                ], 404);
            }

            $history = $customer->with('tbltransaksi.tbldetailtransaksi.tblproduk')
                ->get()
                ->where('ID_Customer', $id)
                ->flatMap(function ($transaksi) {
                    return $transaksi->tbltransaksi->map(function ($detail) {
                        return $detail->tbldetailtransaksi->map(function ($produk) use ($detail) {
                            return [
                                'ID_Transaksi' => $produk->ID_Transaksi,
                                'ID_Produk' => $produk->ID_Produk,
                                'Nama_Produk' => $produk->tblproduk->Nama_Produk,
                                'Harga' => $produk->tblproduk->Harga,
                                'Status' => $detail->Status,
                            ];
                        });
                    });
                })
                ->collapse()
                ->filter(function ($item) {
                    return $item['Status'] == 'Selesai';
                });

            if($history->isEmpty()){
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
}
