<?php

namespace App\Http\Controllers;

use App\Models\tblhampers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TblhampersController extends Controller
{
    public function index() {
        $hampers = tblhampers::with(['tblproduk'])->get();

        if (count($hampers) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $hampers
            ], 200);
        } else {
            return response([
                'message' => 'Empty',
                'data' => null
            ], 404);
        }
    }

    public function store(Request $request) {
        $storedData = $request->all();

        $validate = Validator::make($storedData, [
            'ID_Produk' => 'required',
            'Kartu_Ucapan' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $hampers = tblhampers::find($storedData['ID_Produk']);
        if ($hampers == null) {
            return response([
                'message' => 'Produk Not Found'
            ], 404);
        }

        $tblhampers = tblhampers::create($storedData);
        return response([
            'message' => 'Resep Berhasil Disimpan',
            'data' => $tblhampers
        ], 200);
    }

    public function update(Request $request, $id) {
        $tblhampers = tblhampers::find($id);

        if ($tblhampers == null) {
            return response([
                'message' => 'Hampers Not Found',
                'data' => null
            ], 404);
        }

        $updatedHampers = $request->all();

        $validate = Validator::make($updatedHampers, [
            'Kartu_Ucapan' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $tblhampers->update($request->only('Kartu_Ucapan'));

        if ($request->has('recipes')) {
            $recipes = $request->input('recipes');

            $recipesData = [];
            foreach ($recipes as $data) {
                $recipesData[$data['ID_Produk']] = ['Kuantitas' => $data['Kuantitas']];
            }

            $tblhampers->resep()->sync($recipesData);
        }
        return response([
            'message' => 'Resep Berhasil Diupdate',
            'data' => $tblhampers
        ], 200);
    }

    public function destroy($id) {
        $tblhampers = tblhampers::find($id);

        if ($tblhampers == null) {
            return response([
                'message' => 'Hampers Not Found',
                'data' => null
            ], 404);
        }

        $tblhampers->resep()->detach();
        $tblhampers->delete();
        return response([
            'message' => 'Resep Berhasil Dihapus',
            'data' => $tblhampers
        ], 200);
    }

    public function show($id) {
        $tblhampers = tblhampers::with(['tblproduk'])->find($id);

        if ($tblhampers == null) {
            return response([
                'message' => 'Hampers Not Found',
                'data' => null
            ], 404);
        }

        return response([
            'message' => 'Retrieve Hampers Success',
            'data' => $tblhampers
        ], 200);
    }
}
