<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TblbahanbakuController;
use App\Http\Controllers\TblpegawaiController;
use App\Http\Controllers\TbldetailresepController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// helper Rachell ntar dihapus
Route::post('/registerCustomer', [App\Http\Controllers\AuthController::class, 'registerCustomer']);
Route::post('/registerPegawai', [App\Http\Controllers\AuthController::class, 'registerPegawai']);

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::middleware(['auth:api-pegawai', 'role:Admin,Owner,MO'])->group(function () {
    //Rute yang bisa diakses admin&owner&mo
    Route::post('/logoutPegawai', [App\Http\Controllers\AuthController::class, 'logoutPegawai']);
});

Route::middleware(['auth:api-pegawai', 'role:Admin'])->group(function () {
    //Rute yang cuma bisa diakses Admin
    Route::get('/produk', [App\Http\Controllers\TblprodukController::class, 'index']);
    Route::post('/produk/resep', [App\Http\Controllers\TblprodukController::class, 'storeResep']);
    Route::post('/produk/titipan', [App\Http\Controllers\TblprodukController::class, 'storeTitipan']);
    Route::post('/produk/hampers', [App\Http\Controllers\TblprodukController::class, 'storeHampers']);
    Route::put('/produk/{id}', [App\Http\Controllers\TblprodukController::class, 'update']);
    Route::get('/produk/{id}', [App\Http\Controllers\TblprodukController::class, 'show']);
    Route::delete('/produk/{id}', [App\Http\Controllers\TblprodukController::class, 'destroy']);

    Route::get('/hampers', [App\Http\Controllers\TblhampersController::class, 'index']);
    Route::post('/hampers', [App\Http\Controllers\TblhampersController::class, 'store']);
    Route::get('/hampers/{id}', [App\Http\Controllers\TblhampersController::class, 'show']);
    Route::put('/hampers/{id}', [App\Http\Controllers\TblhampersController::class, 'update']); // update isi hampers by idhampers
    Route::delete('/hampers/{id}', [App\Http\Controllers\TblhampersController::class, 'destroy']); // detach semua isi hampers by idhampers

    Route::get('/titipan', [App\Http\Controllers\TbltitipanController::class, 'index']);
    Route::post('/titipan', [App\Http\Controllers\TbltitipanController::class, 'store']);
    Route::get('/titipan/{id}', [App\Http\Controllers\TbltitipanController::class, 'show']);
    Route::put('/titipan/{id}', [App\Http\Controllers\TbltitipanController::class, 'update']);
    Route::delete('/titipan/{id}', [App\Http\Controllers\TbltitipanController::class, 'destroy']);

    Route::get('/resep', [App\Http\Controllers\TblresepController::class, 'index']);
    Route::post('/resep', [App\Http\Controllers\TblresepController::class, 'store']);
    Route::get('/resep/{id}', [App\Http\Controllers\TblresepController::class, 'show']);
    Route::put('/resep/{id}', [App\Http\Controllers\TblresepController::class, 'update']);
    Route::delete('/resep/{id}', [App\Http\Controllers\TblresepController::class, 'destroy']);

    Route::get('/getPenitipAll', [App\Http\Controllers\TblpenitipController::class, 'index']);
    Route::post('/createPenitip', [App\Http\Controllers\TblpenitipController::class, 'createPenitip']);
    Route::put('/updatePenitip/{id}', [App\Http\Controllers\TblpenitipController::class, 'updatePenitip']);
    Route::delete('/deletePenitip/{id}', [App\Http\Controllers\TblpenitipController::class, 'deletePenitip']);
    Route::post('/productForSpesificPenitip', [App\Http\Controllers\TblpenitipController::class, 'getAllProductByPenitip']);

    Route::get('/customer', [App\Http\Controllers\TblcustomerController::class, 'getAllCustomer']);
    Route::post('/customerSearch', [App\Http\Controllers\TblcustomerController::class, 'searchGetCustomer']);
    Route::get('/customerHistory/{id}', [App\Http\Controllers\TblcustomerController::class, 'getCustomerHistory']);
    Route::get('/customerAddress/{id}', [App\Http\Controllers\TblalamatController::class, 'getSpesificAddressByIdUser']);
    Route::get('/customerTransaction/{id}', [App\Http\Controllers\TbltransaksiController::class, 'getTransaksiToProduk']);
});

Route::middleware(['auth:api-pegawai', 'role:Owner'])->group(function () {
    //Rute yang cuma bisa diakses Owner
});

Route::middleware(['auth:api-pegawai', 'role:MO'])->group(function () {
    //Rute yang cuma bisa diakses MO
    Route::get('/transaksi-bahan', [App\Http\Controllers\TbltransaksibahanbakuController::class, 'index']);
    Route::post('/transaksi-bahan', [App\Http\Controllers\TbltransaksibahanbakuController::class, 'store']);
    Route::get('/transaksi-bahan/{id}', [App\Http\Controllers\TbltransaksibahanbakuController::class, 'show']);
    Route::put('/transaksi-bahan/{id}', [App\Http\Controllers\TbltransaksibahanbakuController::class, 'update']);
    Route::delete('/transaksi-bahan/{id}', [App\Http\Controllers\TbltransaksibahanbakuController::class, 'destroy']);
    
    Route::get('/pengeluaran', [App\Http\Controllers\TblpengeluaranController::class, 'getAllDataPengeluaran']);
    Route::post('/pengeluaran', [App\Http\Controllers\TblpengeluaranController::class, 'createPengeluaran']);
    Route::put('/pengeluaran', [App\Http\Controllers\TblpengeluaranController::class, 'updatePengeluaran']);
    Route::post('/pengeluaranDelete', [App\Http\Controllers\TblpengeluaranController::class, 'deletePengeluaran']);
    Route::post('/pengeluaranSearch', [App\Http\Controllers\TblpengeluaranController::class, 'searchPengeluaran']);

    Route::get('/presensi', [App\Http\Controllers\TblpresensiController::class, 'index']);
    Route::post('/presensi', [App\Http\Controllers\TblpresensiController::class, 'store']);
});

Route::middleware(['auth:api-customer', 'role:Customer'])->group(function () {
    //rute yang cuma bisa diakses customer
    Route::post('/logoutCustomer', [App\Http\Controllers\AuthController::class, 'logoutCustomer']);
});


// Temporary Seto
Route::get('/getBahanBakuAll', [App\Http\Controllers\TblbahanbakuController::class, 'index']);
Route::post('/createBahanBaku', [App\Http\Controllers\TblbahanbakuController::class, 'createBahanBaku']);
Route::put('/updateBahanBaku/{id}', [App\Http\Controllers\TblbahanbakuController::class, 'updateBahanBaku']);
Route::delete('/deleteBahanBaku/{id}', [App\Http\Controllers\TblbahanbakuController::class, 'deleteBahanBaku']);

Route::post('/forget-password', [App\Http\Controllers\TblcustomerController::class, 'forgetPassword']);
Route::post('/checkCredentialToken', [App\Http\Controllers\TblcustomerController::class, 'checkingCredentialToken']);
Route::put('/reset-password', [App\Http\Controllers\TblcustomerController::class, 'resetPassword']);

//Konfirmasi Email
Route::post('/confirm-email', [App\Http\Controllers\TblcustomerController::class, 'confirmEmail']);

// Pegawai Kelvin (ON PROGRESS)

Route::group(['middleware' => 'auth:api-pegawai'], function () {
    Route::get('/pegawai', [TblpegawaiController::class, 'index']);
    Route::post('/pegawai', [TblpegawaiController::class, 'store']);
    Route::get('/pegawai/{data}', [TblpegawaiController::class, 'show']);
    Route::put('/pegawai/{id}', [TblpegawaiController::class, 'update']);
    Route::delete('/pegawai/{id}', [TblpegawaiController::class, 'delete']);
    Route::put('/update-gaji/{id}', [TblpegawaiController::class, 'updateGaji']);
    Route::put('/update-bonus/{id}', [TblpegawaiController::class, 'updateBonus']);
    Route::put('/reset-password/{id}', [TblpegawaiController::class, 'resetPassword']);
});

//Resep Kelvin (ON PROGRESS)
Route::group(['middleware' => 'auth:api-detail-resep'], function() {
    Route::post('/detail-resep', [TbldetailresepController::class, 'store']);
    Route::get('/detail-resep', [TbldetailresepController::class, 'index']);
    Route::get('/detail-resep/{id}', [TbldetailresepController::class, 'show']);
    Route::put('/detail-resep/{idP}/{idBB}', [TbldetailresepController::class, 'update']);
    Route::delete('/detail-resep/{id}', [TbldetailresepController::class, 'delete']);

    Route::post('/detail-resepForRelated', [TbldetailresepController::class, 'showRelatedProduct']);
});

Route::get('/jabatan', [App\Http\Controllers\TbljabatanController::class, 'index']);

Route::group(['middleware'=>'auth:api-customer'], function() {
    Route::get('/customer', [App\Http\Controllers\TblcustomerController::class, 'index']);
    Route::put('/customer/{id}', [App\Http\Controllers\TblcustomerController::class, 'update']);
    Route::post('/customer', [App\Http\Controllers\TblcustomerController::class, 'updateProfile']);
    
    Route::get('/customer/history', [App\Http\Controllers\TbltransaksiController::class, 'getTransaksiCustomer']);
    Route::get('/customer/history/{nama}', [App\Http\Controllers\TbltransaksiController::class, 'searchDataHistoryTransaksi']);
    Route::post('/customer/transaksi', [App\Http\Controllers\TbltransaksiController::class, 'store']); // cmn testing buat show history

    Route::post('/customer/detail-transaksi', [App\Http\Controllers\TbldetailtransaksiController::class, 'store']); // cmn testing buat show history

    Route::get('/customer/alamat', [App\Http\Controllers\TblalamatController::class, 'index']);
    Route::post('/customer/alamat', [App\Http\Controllers\TblalamatController::class, 'store']);

    Route::get('/produk', [App\Http\Controllers\TblprodukController::class, 'index']);
});

Route::get('/transaksi', [App\Http\Controllers\TbltransaksiController::class, 'index']);