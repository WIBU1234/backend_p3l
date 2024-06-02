<?php

namespace App\Http\Controllers;

use App\Models\tblhistorysaldo;
use App\Models\tblcustomer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TblhistorysaldoController extends Controller
{
    public function getAllHistoryTransaction(){
        try{
            $history = tblhistorysaldo::with('tblcustomer')
                ->orderBy('Tanggal', 'DESC')
                ->get();

            if($history->isEmpty()){
                return response()->json([
                    'message' => 'No data found',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'message' => 'Data found',
                'data' => $history
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Failed to get data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function adminAcceptHistory($id){
        try{
            $history = tblhistorysaldo::with('tblcustomer')
                ->where('ID_History', $id)
                ->first();

            if(!$history){
                return response()->json([
                    'message' => 'Data not found',
                    'data' => []
                ], 404);
            }

            if($history->Tanggal != null){
                return response()->json([
                    'message' => 'Data already accepted',
                    'data' => $history
                ], 400);
            }

            if($history->Total > $history->tblcustomer->Saldo){
                return response()->json([
                    'message' => 'Insufficient balance',
                    'data' => [
                        'Penarikan' => $history->Total,
                        'Saldo' => $history->tblcustomer->Saldo
                    ],
                    'table' => $history
                ], 400);
            }

            $customer = tblcustomer::find($history->ID_Customer);

            if(!$customer){
                return response()->json([
                    'message' => 'Customer not found',
                    'data' => []
                ], 404);
            }

            $history->Tanggal = Carbon::now();
            $history->save();

            $customer->Saldo -= $history->Total;
            $customer->save();

            return response()->json([
                'message' => 'Data accepted',
                'data' => $history
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Failed to accept data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function customerGetAllHistory(){
        try{
            $user = Auth::user();

            $history = tblhistorysaldo::with('tblcustomer')
                ->where('ID_Customer', $user->ID_Customer)
                ->orderBy('Tanggal', 'DESC')
                ->get();

            if($history->isEmpty()){
                return response()->json([
                    'message' => 'No data found',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'message' => 'Data found',
                'data' => $history
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Failed to get data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function customerRequestSaldo(Request $request){
        try{
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'Saldo' => 'required|numeric',
            ]);
    
            if($validator->fails()) {
                return response([
                    'message' => $validator->errors(),
                    'status' => 404
                ], 404);
            }

            $cekIn = tblhistorysaldo::with('tblcustomer')
                ->where('Tanggal', null)
                ->where('Total', '>', 0)
                ->first();

            if($cekIn){
                return response()->json([
                    'message' => 'You can only make one request at a time',
                    'data' => $cekIn
                ], 400);
            }

            if($request['Saldo'] <= 0){
                return response()->json([
                    'message' => 'Invalid request',
                    'data' => 'Saldo must be greater than 0'
                ], 400);
            }

            if($user->Saldo < $request['Saldo']){
                return response()->json([
                    'message' => 'Insufficient balance',
                    'data' => [
                        'Penarikan' => $request['Saldo'],
                        'Saldo' => $user->Saldo
                    ]
                ], 400);
            }

            $history = tblhistorysaldo::create([
                'ID_Customer' => $user->ID_Customer,
                'Tanggal' => null,
                'Total' => $request['Saldo']
            ]);

            $userFound = tblcustomer::find($user->ID_Customer)
                ->update([
                    'Saldo' => $user->Saldo - $request['Saldo']
                ]);

            return response()->json([
                'message' => 'Request sent',
                'data' => 
                    [
                        $history, 
                        $userFound
                    ],
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Failed to send request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
}
