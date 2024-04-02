<?php

use App\Http\Controllers\AuthController;
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

Route::middleware(['auth:api-pegawai', 'role:Owner'])->group(function () {
    Route::post('/logoutPegawai', [App\Http\Controllers\AuthController::class, 'logoutPegawai']);
});

Route::middleware(['auth:api-customer', 'role:Customer'])->group(function () {
    Route::post('/logoutCustomer', [App\Http\Controllers\AuthController::class, 'logoutCustomer']);
});