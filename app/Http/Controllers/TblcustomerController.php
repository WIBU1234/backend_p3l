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
use Illuminate\Support\Facades\Mail;

class TblcustomerController extends Controller
{
    use Notifiable, CanResetPassword;

    public function forgetPassword(Request $request)
    {
        try{
            $user = tblcustomer::where('email', $request->email)->get();

            if ($user->isEmpty()) {
                return response()->json([
                    'message' => 'Gk ada emailnya',
                ], 404);
            }

            $token = Str::random(40);
            $domain = URL::to('/');
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

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Gagal',
                'data' => $e
            ], 400);
        }
    }
}
