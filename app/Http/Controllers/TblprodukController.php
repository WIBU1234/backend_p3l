<?php

namespace App\Http\Controllers;

use App\Models\tblproduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class TblprodukController extends Controller
{
    public function index()
    {
        $produk = tblproduk::all();

        if(count($produk) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $produk
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function storeResep(Request $request)
    {
        try {
            $initID = 'AK';
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'ID_Kategori' => 'required',
                'Nama_Produk' => 'required',
                'Harga' => 'required',
                'Stok' => 'nullable',
                'StokReady' => 'nullable',
                'Gambar' => 'required|image:jpeg,png,jpg|max:2048'
            ]);

            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ], 400);
            }

            // upload gambar produk pada public/img disimpan path nya
            $uploadFolder = 'img';
            $gambarProduk = $request->file('Gambar');

            $gambarProdukFiles = $gambarProduk->store($uploadFolder, 'public');
            $gambarProdukPath = basename($gambarProdukFiles);

            // pengaturan nilai stok dan stokready
            $stok = $request->has('Stok') ? $request->Stok : 0;
            $StokReady = $request->has('StokReady') ? $request->StokReady : 0;

            // Pengaturan ID resep
            $id = $initID . $this->generateIDProduk($initID);

            $produk = tblproduk::create([
                'ID_Produk' => $id,
                'ID_Kategori' => $request->ID_Kategori,
                'Nama_Produk' => $request->Nama_Produk,
                'Harga' => $request->Harga,
                'Stok' => $stok,
                'StokReady' => $StokReady,
                'Gambar' => $gambarProdukPath
            ]);

            return response([
                'message' => 'Add Content Success',
                'data' => $produk
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
                'id' => $id,
                'data' => []
            ], 400);
        }
    }

    public function storeTitipan(Request $request)
    {
        try {
            $initID = 'PN';
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'ID_Kategori' => 'required',
                'Nama_Produk' => 'required',
                'Harga' => 'required',
                'Stok' => 'nullable',
                'StokReady' => 'nullable',
                'Gambar' => 'required|image:jpeg,png,jpg|max:2048'
            ]);

            if($validate->fails()){
                return response(['message' => $validate->errors()], 400);
            }

            // upload gambar produk pada public/img disimpan path nya
            $uploadFolder = 'img';
            $gambarProduk = $request->file('Gambar');

            $gambarProdukFiles = $gambarProduk->store($uploadFolder, 'public');
            $gambarProdukPath = basename($gambarProdukFiles);

            // pengaturan nilai stok dan stokready
            $stok = $request->has('Stok') ? $request->Stok : 0;
            $StokReady = $request->has('StokReady') ? $request->StokReady : 0;

            // Pengaturan ID resep
            $id = $initID . $this->generateIDProduk($initID);

            $produk = tblproduk::create([
                'ID_Produk' => $id,
                'ID_Kategori' => $request->ID_Kategori,
                'Nama_Produk' => $request->Nama_Produk,
                'Harga' => $request->Harga,
                'Stok' => $stok,
                'StokReady' => $StokReady,
                'Gambar' => $gambarProdukPath
            ]);

            return response([
                'Add Content Success',
                'data' => $produk
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
                'id' => $id,
                'data' => []
            ], 400);
        }
    }

    public function storeHampers(Request $request)
    {
        try {
            $initID = 'HM';
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'ID_Kategori' => 'required',
                'Nama_Produk' => 'required',
                'Harga' => 'required',
                'Stok' => 'nullable',
                'StokReady' => 'nullable',
                'Gambar' => 'required|image:jpeg,png,jpg|max:2048'
            ]);

            if($validate->fails()){
                return response(['message' => $validate->errors()], 400);
            }

            // upload gambar produk pada public/img disimpan path nya
            $uploadFolder = 'img';
            $gambarProduk = $request->file('Gambar');

            $gambarProdukFiles = $gambarProduk->store($uploadFolder, 'public');
            $gambarProdukPath = basename($gambarProdukFiles);

            // pengaturan nilai stok dan stokready
            $stok = $request->has('Stok') ? $request->Stok : 0;
            $StokReady = $request->has('StokReady') ? $request->StokReady : 0;

            // Pengaturan ID resep
            $id = $initID . $this->generateIDProduk($initID);

            $produk = tblproduk::create([
                'ID_Produk' => $id,
                'ID_Kategori' => $request->ID_Kategori,
                'Nama_Produk' => $request->Nama_Produk,
                'Harga' => $request->Harga,
                'Stok' => $stok,
                'StokReady' => $StokReady,
                'Gambar' => $gambarProdukPath
            ]);

            return response([
                'Add Content Success',
                'data' => $produk
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
                'id' => $id,
                'data' => []
            ], 400);
        }
    }

    private function generateIDProduk(string $initID)
    {
        $latestProduk = tblproduk::latest('ID_Produk')->first();
        if ($latestProduk) {
            $latestIndex = intval(substr($latestProduk->ID_Produk, 2));
            $index = $latestIndex + 1;
        } else {
            $index = 1;
        }

        do {
            $id = sprintf('%02d', $index);
            $existingProduk = tblproduk::where('ID_Produk', $initID . $id)->first();
            $index++;
        } while ($existingProduk);

        return $id;
    }

    public function update(Request $request, string $id)
    {
        $produk = tblproduk::find($id);
        if (is_null($produk)) {
            return response([
                'message' => 'Content Not Found',
                'data' => null
            ], 404);
        }

        $updatedData = $request->all();
        $validate = Validator::make($updatedData, [
            'ID_Kategori' => 'required',
            'Nama_Produk' => 'required',
            'Harga' => 'required',
            'Stok' => 'nullable',
            'StokReady' => 'nullable'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        if ($request->hasFile('Gambar')) {
            $uploadFolder = 'img';
            $gambarProduk = $request->file('Gambar');

            $gambarProdukFiles = $gambarProduk->store($uploadFolder, 'public');
            $gambarProdukPath = basename($gambarProdukFiles);

            Storage::disk('public')->delete('img/'.$produk->Gambar);

            $updatedData['Gambar'] = $gambarProdukPath;
            $produk->Gambar = $updatedData['Gambar'];
        }

        // pengaturan nilai stok dan stokready
        $stok = $request->has('Stok') ? $request->Stok : 0;
        $StokReady = $request->has('StokReady') ? $request->StokReady : 0;

        $produk->ID_Kategori = $updatedData['ID_Kategori'];
        $produk->Nama_Produk = $updatedData['Nama_Produk'];
        $produk->Harga = $updatedData['Harga'];
        $produk->Stok = $stok;
        $produk->StokReady = $StokReady;

        if ($produk->save()) {
            return response([
                'message' => 'Update Produk Success',
                'data' => $produk
            ], 200);
        }
        
        return response([
            'message' => 'Update Content Failed',
            'data' => null 
        ], 400);
    }

    public function show(string $id)
    {
        $produk =  tblproduk::find($id);

        if (!is_null($produk)) {
            return response([
                'message' => 'Produk found',
                'data' => $produk
            ], 200);
        }

        return response([
            'message' => 'Produk not found',
            'data' => null
        ], 404);
    }

    public function destroy(string $id)
    {
        $produk = tblproduk::find($id);

        if (is_null($produk)) {
            return response([
                'message' => 'Produk not found', 
                'data' => null 
            ], 404);
        }

        if ($produk->delete()) {
            return response([
                'message' => 'Delete produk Success',
                'data' => $produk
            ], 200);
        }

        return response([
            'message' => 'Delete Produk Failed',
            'data' => null
        ], 400);
    }

    public function getAllProdukForFrontEnd(){
        try{
            $produk = tblproduk::with(['tblkategori'])->get();

            if(count($produk) > 0){
                return response([
                    'message' => 'Retrieve All Produk Success',
                    'data' => $produk
                ], 200);
            }

            return response([
                'message' => 'Empty',
                'data' => null
            ], 400);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }

    public function pickRandomFourProduct(){
        try{
            $produk = tblproduk::with('tblkategori')->get()->random(4);

            if(count($produk) > 0){
                return response([
                    'message' => 'Retrieve Random Four Produk Success',
                    'data' => $produk
                ], 200);
            }

            return response([
                'message' => 'Empty',
                'data' => null
            ], 400);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }
}
