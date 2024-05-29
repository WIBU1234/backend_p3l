<?php

namespace App\Http\Controllers;

use App\Models\tblhampers;
use App\Models\tblproduk;
use App\Models\tblresep;
use App\Models\tbltitipan;
use App\Models\tbltransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class TblprodukController extends Controller
{
    public function index()
    {
        $produk = tblproduk::with(['kategori'])->get();

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
            //$uploadFolder = 'img';
            $gambarProduk = $request->file('Gambar');
            $originalName = $gambarProduk->getClientOriginalName();

            $cloudinaryController = new cloudinaryController();
            $public_id = $cloudinaryController->sendImageToCloudinary($gambarProduk, $originalName);


            //$gambarProdukFiles = $gambarProduk->store($uploadFolder, 'public');
            //$gambarProdukPath = basename($gambarProdukFiles);

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
                'Gambar' => $public_id
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
                'StokReady' => 'required',
                'Gambar' => 'required|image:jpeg,png,jpg|max:2048'
            ]);

            if($validate->fails()){
                return response(['message' => $validate->errors()], 400);
            }

            // upload gambar produk pada public/img disimpan path nya
            //$uploadFolder = 'img';
            $gambarProduk = $request->file('Gambar');
            $originalName = $gambarProduk->getClientOriginalName();

            $cloudinaryController = new cloudinaryController();
            $public_id = $cloudinaryController->sendImageToCloudinary($gambarProduk, $originalName);

            // $gambarProdukFiles = $gambarProduk->store($uploadFolder, 'public');
            // $gambarProdukPath = basename($gambarProdukFiles);

            // pengaturan nilai stok dan stokready
            //$stok = $request->has('Stok') ? $request->Stok : 0;
            $StokReady = $request->has('StokReady') ? $request->StokReady : 0;

            // Pengaturan ID resep
            $id = $initID . $this->generateIDProduk($initID);

            $produk = tblproduk::create([
                'ID_Produk' => $id,
                'ID_Kategori' => $request->ID_Kategori,
                'Nama_Produk' => $request->Nama_Produk,
                'Harga' => $request->Harga,
                'Stok' => $request->Stok,
                'StokReady' => $StokReady,
                'Gambar' => $public_id
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
            //$uploadFolder = 'img';
            $gambarProduk = $request->file('Gambar');
            $originalName = $gambarProduk->getClientOriginalName();

            $cloudinaryController = new cloudinaryController();
            $public_id = $cloudinaryController->sendImageToCloudinary($gambarProduk, $originalName);

            // $gambarProdukFiles = $gambarProduk->store($uploadFolder, 'public');
            // $gambarProdukPath = basename($gambarProdukFiles);

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
                'Gambar' => $public_id
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

    public function showProductToday()
    {
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $lusa = Carbon::now('Asia/Jakarta')->addDays(2)->format('Y-m-d');
        $limit = 20;

        $transaksiPO = tbltransaksi::where('Tanggal_Ambil', $lusa)
                                ->where('Status', '!=', 'Ditolak')->with(['products'])->get();

        $transaksiReady = tbltransaksi::whereBetween('Tanggal_Ambil', [$today, $lusa])
                                ->where('Status', '!=', 'Ditolak')->with(['products'])->get();
        // Save Stock PO
        $productQty = [];
        
        //kumpulin semua kuantitas produk di array productQty
        foreach ($transaksiPO as $trans) {
            foreach ($trans->products as $product) {
                if (!isset($productQty[$product->ID_Produk]) && $product->ID_Kategori !== 4) {
                    $productQty[$product->ID_Produk] = $product->pivot->Kuantitas;
                } elseif ($product->ID_Kategori !== 4) {
                    $productQty[$product->ID_Produk] += $product->pivot->Kuantitas;
                }
            }
        }

        foreach ($transaksiReady as $trans) {
            foreach ($trans->products as $product) {
                if (!isset($productQty[$product->ID_Produk]) && $product->ID_Kategori === 4) {
                    $productQty[$product->ID_Produk] = $product->pivot->Kuantitas;
                } elseif ($product->ID_Kategori === 4) {
                    $productQty[$product->ID_Produk] += $product->pivot->Kuantitas;
                }
            }
        }
        
        $produk = tblproduk::with(['kategori'])->get();
        
        //Kurangi limit dengan array kuantitas
        foreach ($produk as $prod) {
            if (isset($productQty[$prod->ID_Produk])) {
                if ($prod->kategori->Nama_Kategori !== 'Titipan') {
                    $prod->Stok = $limit - $productQty[$prod->ID_Produk];
                } else {
                    $prod->StokReady -= $productQty[$prod->ID_Produk];
                }
            } else {
                if ($prod->kategori->Nama_Kategori !== 'Titipan') {
                    $prod->Stok = $limit;
                }
            }
        }

        if (count($produk) > 0) {
            return response([
                'message' => 'Produk found',
                'transaksi PO' => $productQty,
                'data' => $produk
            ], 200);
        }

        return response([
            'message' => 'Produk not found',
            'data' => null
        ], 404);
    }

    public function showProductByTglAmbil(String $date)
    {
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $limit = 20;

        $transaksiPO = tbltransaksi::where('Tanggal_Ambil', $date)
                                ->where('Status', '!=', 'Ditolak')->with(['products'])->get();

        $transaksiReady = tbltransaksi::whereBetween('Tanggal_Ambil', [$today, $date])
                                ->where('Status', '!=', 'Ditolak')->with(['products'])->get();
        // Save Stock PO
        $productQty = [];
        
        //kumpulin semua kuantitas produk di array productQty
        foreach ($transaksiPO as $trans) {
            foreach ($trans->products as $product) {
                if (!isset($productQty[$product->ID_Produk]) && $product->ID_Kategori !== 4) {
                    $productQty[$product->ID_Produk] = $product->pivot->Kuantitas;
                } elseif ($product->ID_Kategori !== 4) {
                    $productQty[$product->ID_Produk] += $product->pivot->Kuantitas;
                }
            }
        }

        foreach ($transaksiReady as $trans) {
            foreach ($trans->products as $product) {
                if (!isset($productQty[$product->ID_Produk]) && $product->ID_Kategori === 4) {
                    $productQty[$product->ID_Produk] = $product->pivot->Kuantitas;
                } elseif ($product->ID_Kategori === 4) {
                    $productQty[$product->ID_Produk] += $product->pivot->Kuantitas;
                }
            }
        }
        
        $produk = tblproduk::with(['kategori'])->get();
        
        //Kurangi limit dengan array kuantitas
        foreach ($produk as $prod) {
            if (isset($productQty[$prod->ID_Produk])) {
                if ($prod->kategori->Nama_Kategori !== 'Titipan') {
                    $prod->Stok = $limit - $productQty[$prod->ID_Produk];
                } else {
                    $prod->StokReady -= $productQty[$prod->ID_Produk];
                }
            } else {
                if ($prod->kategori->Nama_Kategori !== 'Titipan') {
                    $prod->Stok = $limit;
                }
            }
        }

        if (count($produk) > 0) {
            return response([
                'message' => 'Produk found',
                'transaksi PO' => $productQty,
                'data' => $produk
            ], 200);
        }

        return response([
            'message' => 'Produk not found',
            'data' => null
        ], 404);
    }

    public function reduceStok(string $id_trans)
    {
        //Mengurangi kuota hampers dan resep saja
        $transaksi = tbltransaksi::where('ID_Transaksi', $id_trans)->first();

        if (!$transaksi || count($transaksi->products) == 0) {
            return response([
                'message' => 'Transaksi tidak ditemukan',
                'data' => null
            ], 404);
        }

        $updatedProducts = [];
        $hampers = tblhampers::with(['tblproduk', 'resep'])->get();

        foreach ($hampers as $hamper) {
            foreach ($transaksi->products as $prod) {
                if ($hamper->ID_Produk == $prod->ID_Produk) {
                    $hamper->tblproduk->Stok -= $prod->pivot->Kuantitas;
                    $hamper->tblproduk->save();
                    $updatedProducts[] = $hamper->tblproduk;
                }
            }
        }

        $homecooks = tblresep::with(['tblproduk'])->get();

        foreach ($homecooks as $homecook) {
            foreach ($transaksi->products as $prod) {
                if ($homecook->ID_Produk == $prod->ID_Produk) {
                    $homecook->tblproduk->Stok -= $prod->pivot->Kuantitas;
                    $homecook->tblproduk->save();
                    $updatedProducts[] = $homecook->tblproduk;
                }
            }
        }

        if (count($updatedProducts) > 0) {
            return response([
                'message' => 'Update Produk Success',
                'updated_products' => $updatedProducts
            ], 200);
        }
        
        return response([
            'message' => 'Update Content Failed',
            'data' => $transaksi
        ], 400);
    }

    public function reduceReady(string $id_trans)
    {
        $transaksi = tbltransaksi::where('ID_Transaksi', $id_trans)->first();

        if (!$transaksi || count($transaksi->products) == 0) {
            return response([
                'message' => 'Transaksi tidak ditemukan',
                'data' => null
            ], 404);
        }

        $updatedProducts = [];
        $hampers = tblhampers::with(['tblproduk', 'resep'])->get();

        foreach ($hampers as $hamper) {
            foreach ($transaksi->products as $prod) {
                if ($hamper->ID_Produk == $prod->ID_Produk) {
                    $hamper->tblproduk->StokReady -= $prod->pivot->Kuantitas;
                    $hamper->tblproduk->save();
                    $updatedProducts[] = $hamper->tblproduk;
                }
            }
        }

        $homecooks = tblresep::with(['tblproduk'])->get();

        foreach ($homecooks as $homecook) {
            foreach ($transaksi->products as $prod) {
                if ($homecook->ID_Produk == $prod->ID_Produk) {
                    $homecook->tblproduk->StokReady -= $prod->pivot->Kuantitas;
                    $homecook->tblproduk->save();
                    $updatedProducts[] = $homecook->tblproduk;
                }
            }
        }

        $titipan = tbltitipan::with(['tblproduk'])->get();

        foreach ($titipan as $titip) {
            foreach ($transaksi->products as $prod) {
                if ($titip->ID_Produk == $prod->ID_Produk) {
                    $titip->tblproduk->StokReady -= $prod->pivot->Kuantitas;
                    $titip->tblproduk->save();
                    $updatedProducts[] = $titip->tblproduk;
                }
            }
        }

        if (count($updatedProducts) > 0) {
            return response([
                'message' => 'Update Produk Success',
                'updated_products' => $updatedProducts
            ], 200);
        }
        
        return response([
            'message' => 'Update Content Failed',
            'data' => $transaksi
        ], 400);
    }

    public function destroy(string $id)
    {
        $produk = tblproduk::find($id);
        $cloudinaryController = new cloudinaryController();
        $response = $cloudinaryController->deleteImageFromCloudinary($produk->Gambar);


        if (is_null($produk)) {
            return response([
                'message' => 'Produk not found', 
                'data' => null 
            ], 404);
        }

        if ($produk->delete()) {
            return response([
                'message' => 'Delete produk Success',
                'image_status' => $response,
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
            $produk = tblproduk::all();

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
}
