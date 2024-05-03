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
    Route::get('/customerHistory/{id}', [App\Http\Controllers\TblcustomerController::class, 'getCustomerHistory']);
    Route::get('/customerAddress/{id}', [App\Http\Controllers\TblalamatController::class, 'getSpesificAddressByIdUser']);
});

Route::middleware(['auth:api-pegawai', 'role:Owner'])->group(function () {
    //Rute yang cuma bisa diakses Owner
});

Route::middleware(['auth:api-pegawai', 'role:MO'])->group(function () {
    //Rute yang cuma bisa diakses MO
    Route::get('/pengeluaran', [App\Http\Controllers\TblpengeluaranController::class, 'getAllDataPengeluaran']);
    Route::post('/pengeluaran', [App\Http\Controllers\TblpengeluaranController::class, 'createPengeluaran']);
    Route::put('/pengeluaran', [App\Http\Controllers\TblpengeluaranController::class, 'updatePengeluaran']);
    Route::post('/pengeluaranDelete', [App\Http\Controllers\TblpengeluaranController::class, 'deletePengeluaran']);
    Route::post('/pengeluaranSearch', [App\Http\Controllers\TblpengeluaranController::class, 'searchPengeluaran']);
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

// Pegawai Kelvin (ON PROGRESS)
Route::group(['middleware' => 'auth:api-pegawai'], function () {
    Route::get('/pegawai', [TblpegawaiController::class, 'index']);
    Route::post('/pegawai', [TblpegawaiController::class, 'store']);
    Route::get('/pegawai/{data}', [TblpegawaiController::class, 'show']);
    Route::put('/pegawai/{id}', [TblpegawaiController::class, 'update']);
    Route::delete('/pegawai/{id}', [TblpegawaiController::class, 'delete']);
    Route::put('/update-gaji/{id}', [TblpegawaiController::class, 'updateGaji']);
    Route::put('/update-bonus/{id}', [TblpegawaiController::class, 'updateBonus']);
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
