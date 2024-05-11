<?php

namespace App\Http\Controllers;

use App\Models\tblbahanbaku;
use App\Models\tbltransaksibahanbaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TbltransaksibahanbakuController extends Controller
{
    public function index() {
        $transaksibb = tbltransaksibahanbaku::with('bahanbaku')->get();

        if (count($transaksibb) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksibb
            ], 200);
        } else {
            return response([
                'message' => 'Empty',
                'data' => null
            ], 400);
        }
    }

    public function store(Request $request) {
        $storedData = $request->all();

        $validate = Validator::make($storedData, [
            'Tanggal' => 'required'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $transaksibb = tbltransaksibahanbaku::create([
            'Tanggal' => $request->Tanggal
        ]);

        if ($request->has('bahanbaku')) {
            $bahanbaku = $request->input('bahanbaku');

            $bahanbakuData = [];
            foreach ($bahanbaku as $data) {
                $bahanbakuData[$data['ID_Bahan_Baku']] = [
                    'Kuantitas' => $data['Kuantitas'],
                    'Sub_Total' => $data['Sub_Total'] 
                ];

                $this->addStok($data['ID_Bahan_Baku'], $data['Kuantitas']);
            }

            $transaksibb->bahanbaku()->attach($bahanbakuData);
        }

        return response([
            'message' => 'Transaksi Berhasil',
            'data' => $transaksibb
        ], 200);
    }

    public function update(Request $request, $id) {
        $transaksibb = tbltransaksibahanbaku::find($id);

        if ($transaksibb == null) {
            return response([
                'message' => 'Transaksi are not found',
                'data' => null
            ], 404);
        }

        $updatedData = $request->all();

        $validate = Validator::make($updatedData, [
            'Tanggal' => 'required'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $this->deleteStok($transaksibb);

        $transaksibb->update($request->only('Tanggal'));

        if ($request->has('bahanbaku')) {
            $bahanbaku = $request->input('bahanbaku');

            $bahanbakuData = [];
            foreach ($bahanbaku as $data) {
                $bahanbakuData[$data['ID_Bahan_Baku']] = [
                    'Kuantitas' => $data['Kuantitas'],
                    'Sub_Total' => $data['Sub_Total'] 
                ];

                $this->addStok($data['ID_Bahan_Baku'], $data['Kuantitas']);
            }

            $transaksibb->bahanbaku()->sync($bahanbakuData);
        }

        return response([
            'message' => 'Transaksi berhasil diupdate',
            'data' => $transaksibb
        ], 200);
    }

    public function destroy($id) {
        $transaksibb = tbltransaksibahanbaku::find($id);

        if ($transaksibb ==  null) {
            return response([
                'message' => 'Transaksi Not Found',
                'data' => null
            ], 404);
        }

        $transaksibb->bahanbaku()->detach();
        $transaksibb->delete();
        return response([
            'message' => 'Transaksi Berhasil Dihapus',
            'data' => $transaksibb
        ], 200);
    }

    public function show($id) {
        $transaksibb = tbltransaksibahanbaku::with('bahanbaku')->find($id);

        if ($transaksibb == null) {
            return response([
                'message' => 'Transaksi Not Found',
                'data' => null
            ], 404);
        }

        return response([
            'message' => 'Retrieve Hampers Success',
            'data' => $transaksibb
        ], 200);
    }

    private function addStok($bahanBakuID, $quantity) {
        $bahanbaku = tblbahanbaku::find($bahanBakuID);

        if ($bahanbaku) {
            $bahanbaku->Stok += $quantity;
            $bahanbaku->save();
        }
    }

    private function deleteStok($transaksi) {
        foreach ($transaksi->bahanbaku as $bahanbaku) {
            $bahan = tblbahanbaku::find($bahanbaku->ID_Bahan_Baku);
            if ($bahan) {
                $bahan->Stok -= $bahanbaku->pivot->Kuantitas;
                $bahan->save();
            }
        }
    }
}
