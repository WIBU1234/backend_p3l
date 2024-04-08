<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TblbahanbakuController;
use App\Http\Controllers\TblpegawaiController;
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
});

Route::middleware(['auth:api-pegawai', 'role:Owner'])->group(function () {
    //Rute yang cuma bisa diakses Owner
});

Route::middleware(['auth:api-pegawai', 'role:MO'])->group(function () {
    //Rute yang cuma bisa diakses MO
});

Route::middleware(['auth:api-customer', 'role:Customer'])->group(function () {
    //rute yang cuma bisa diakses customer
    Route::post('/logoutCustomer', [App\Http\Controllers\AuthController::class, 'logoutCustomer']);
});


// Temporary Seto
Route::get('/getBahanBakuALll', [App\Http\Controllers\TblbahanbakuController::class, 'index']);
Route::post('/createBahanBaku', [App\Http\Controllers\TblbahanbakuController::class, 'createBahanBaku']);
Route::post('/updateBahanBaku/{id}', [App\Http\Controllers\TblbahanbakuController::class, 'updateBahanBaku']);

// Route::middleware('auth:api')->group(function () {

// });

// Pegawai Kelvin (ON PROGRESS)
Route::group(['middleware' => 'auth:api-pegawai'], function () {
    Route::get('/pegawai', [TblpegawaiController::class, 'index']);
    Route::post('/pegawai', [TblpegawaiController::class, 'store']);
    Route::get('/pegawai/{nama}', [TblpegawaiController::class, 'show']);
    Route::put('/pegawai/{id}', [TblpegawaiController::class, 'update']);
    Route::delete('/pegawai/{id}', [TblpegawaiController::class, 'delete']);
});