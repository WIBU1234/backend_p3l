<?php

namespace App\Http\Controllers;

use App\Models\tblproduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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
            $id = 'AK' . $this->generateIDProduk();

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

    public function storeTitipan(Request $request)
    {
        try {
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
            $id = 'PN' . $this->generateIDProduk();

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
            $id = 'HM' . $this->generateIDProduk();

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

    private function generateIDProduk()
    {
        $latestProduk = tblproduk::latest('ID_Produk')->first();
        if ($latestProduk) {
            $index = intval(substr($latestProduk->ID_Produk, 3)) + 1;
        } else {
            $index = 1;
        }

        $produkId = sprintf('%03d', $index);

        return $produkId;
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
            'StokReady' => 'nullable',
            'Gambar' => 'image:jpeg,png,jpg|max:2048'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        // upload gambar produk pada public/img disimpan path nya
        // $uploadFolder = 'img';
        // $gambarProduk = $request->file('Gambar');

        // $gambarProdukFiles = $gambarProduk->store($uploadFolder, 'public');
        // $gambarProdukPath = basename($gambarProdukFiles);

        // pengaturan nilai stok dan stokready
        $stok = $request->has('Stok') ? $request->Stok : 0;
        $StokReady = $request->has('StokReady') ? $request->StokReady : 0;

        $produk->ID_Kategori = $updatedData['ID_Kategori'];
        $produk->Nama_Produk = $updatedData['Nama_Produk'];
        $produk->Harga = $updatedData['Harga'];
        $produk->Stok = $stok;
        $produk->StokReady = $StokReady;
        //$produk->Gambar = $gambarProdukPath;

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
}
