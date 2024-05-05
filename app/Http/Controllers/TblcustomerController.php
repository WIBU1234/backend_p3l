<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\tblcustomer;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TblcustomerController extends Controller
{
    use Notifiable, CanResetPassword;

    public function forgetPassword(Request $request)
    {
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
            // $data['token'] = $token;
            $data['email'] = $request->email;
            $data['title'] = 'Reset Password';
            $data['body'] = 'Silahkan klik link dibawah ini untuk mereset password anda';

            Mail::send('forgotPasswordMail', ['data'=>$data], function($message) use ($data){
                $message->to($data['email'])->subject($data['title']);
            });

            // try {
            //     Mail::send('forgotPasswordMail', ['data'=>$data], function($message) use ($data){
            //         $message->to($data['email'])->subject($data['title']);
            //     });
            // } catch (\Exception $e) {
            //     Log::error('Mail sending failed:', [
            //         'message' => $e->getMessage(),
            //     ]);
            // }

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
                'data' => $e->getMessage() // change this to get the actual error message
            ], 400);
        }
    }

    public function checkingCredentialToken(Request $request)
    {
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

    public function resetPassword(Request $request)
    {
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
        
        if (!$user) {
            return response()->json([
                'message' => 'User Not Found',
            ], 404);
        } else {
            return response()->json([
                'message' => 'User Found',
                'data' => $user
            ], 200);
        }
    }

    public function update(request $request) {
        $user = Auth::user();

        try {
            $tblcustomer = tblcustomer::find($user->id);
            $updatecustomer = $request->all();
            $validate = Validator::make($updatecustomer, [
                'Nama_Customer' => 'required',
                'email' => 'required',
                'Nomor_Telepon' => 'required'
            ]);

            if ($validate->fails()) {
                return response([
                    'message' => $validate->errors(),
                ], 404);
            }

            $tblcustomer->update($updatecustomer);

            return response()->json([
                'message' => 'User Updated',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User Not Found',
            ], 404);
        }
    }
}
