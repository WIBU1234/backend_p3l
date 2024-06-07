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

Route::get('/kategoriAll', [App\Http\Controllers\TblkategoriController::class, 'getAllKategori']);
Route::get('/getProductAllForFrontEnd', [App\Http\Controllers\TblprodukController::class, 'getAllProdukForFrontEnd']);

Route::middleware(['auth:api-pegawai', 'role:Admin,Owner,MO'])->group(function () {
    //Rute yang bisa diakses admin&owner&mo
    Route::post('/logoutPegawai', [App\Http\Controllers\AuthController::class, 'logoutPegawai']);
});

Route::middleware(['auth:api-pegawai', 'role:Admin,MO'])->group(function() {
    // Rute yang bisa diakses admin$MO
    Route::get('/getPenitipAll', [App\Http\Controllers\TblpenitipController::class, 'index']);
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

    
    Route::get('/customerGetAll', [App\Http\Controllers\TblcustomerController::class, 'getAllCustomer']);
    Route::post('/customerSearch', [App\Http\Controllers\TblcustomerController::class, 'searchGetCustomer']);
    Route::get('/customerHistory/{id}', [App\Http\Controllers\TblcustomerController::class, 'getCustomerHistory']);
    Route::get('/customerAddress/{id}', [App\Http\Controllers\TblalamatController::class, 'getSpesificAddressByIdUser']);
    Route::get('/customerTransaction/{id}', [App\Http\Controllers\TbltransaksiController::class, 'getTransaksiToProduk']);

    Route::get('/history', [App\Http\Controllers\TblhistorysaldoController::class, 'getAllHistoryTransaction']);
    Route::put('/history/{id}', [App\Http\Controllers\TblhistorysaldoController::class, 'adminAcceptHistory']);

    Route::get('/alamat', [App\Http\Controllers\TblalamatController::class, 'ShowAllAlamat']);
    Route::get('/alamat/no-jarak', [App\Http\Controllers\TblalamatController::class, 'ShowAllAlamatWithNoJarak']);
    Route::get('/alamat/{idC}/{idA}', [App\Http\Controllers\TblalamatController::class, 'ShowOneAlamat']);
    Route::put('/update-jarak-biaya/{idC}/{idA}', [App\Http\Controllers\TblalamatController::class, 'updateJarakBiaya']);

    Route::get('/confirm-transaksi', [App\Http\Controllers\TbltransaksiController::class, 'getTransaksiOnProcess']);
    Route::put('/confirm-transaksi/{id}', [App\Http\Controllers\TbltransaksiController::class, 'updateStatusTransaksi']);

    Route::get('/bukti-pembayaran/{id}', [App\Http\Controllers\TbltransaksiController::class, 'getBuktiPembayaran']);

    Route::get('/transaksi-total-bayar', [App\Http\Controllers\TbltransaksiController::class, 'showTransaksiNoBayar']);
    Route::put('/transaksi-total-bayar/{id}', [App\Http\Controllers\TbltransaksiController::class, 'updateTotalBayarTransaksi']);

    Route::get('/transaksi-diproses', [App\Http\Controllers\TbltransaksiController::class, 'showTransaksiDiproses']);
    Route::put('/transaksi-diproses/{id}', [App\Http\Controllers\TbltransaksiController::class, 'UpdateStatusKirimTransaksi']);

    Route::get('/transaksi-siap-kirim', [App\Http\Controllers\TbltransaksiController::class, 'ShowTransaksiSiapKirim']);
    Route::put('/transaksi-siap-kirim/{id}', [App\Http\Controllers\TbltransaksiController::class, 'UpdateStatusSelesaiTransaksi']);

    Route::get('/transaksi-telat-bayar', [App\Http\Controllers\TbltransaksiController::class, 'showTransaksiExpired']);
    Route::put('/transaksi-telat-bayar/{id}', [App\Http\Controllers\TbltransaksiController::class, 'PutTransaksiTelatBayar']);
    route::get('/detail-transaksi/{id}', [App\Http\Controllers\TbldetailtransaksiController::class, 'ShowDetailTransaksi']);
});

Route::middleware(['auth:api-pegawai', 'role:Owner'])->group(function () {
    //Rute yang cuma bisa diakses Owner
    Route::get('/owner/laporan-penggunaan-bahan-baku/{tglAwal}/{tglAkhir}', [App\Http\Controllers\TblpenggunaanbahanbakuController::class, 'LaporanPenggunaanBahanBaku']);
    Route::get('/owner/laporan-penjualan-tahunan/{tahun}', [App\Http\Controllers\TbltransaksiController::class, 'LaporanPenjualanTahunan']);

    Route::get('/owner/laporan-penjualan-bulanan/{bulan}/{tahun}', [App\Http\Controllers\TbltransaksiController::class, 'laporanPenjualanBulanan']);
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

    Route::put('/updatePengeluaranByID/{id}', [App\Http\Controllers\TblpengeluaranController::class, 'updatePengeluaranByID']);
    Route::delete('/deletePengeluaranByID/{id}', [App\Http\Controllers\TblpengeluaranController::class, 'deletePengeluaranByID']);

    Route::get('/presensi', [App\Http\Controllers\TblpresensiController::class, 'index']);
    Route::post('/presensi', [App\Http\Controllers\TblpresensiController::class, 'store']);
    Route::put('/presensi/{id}', [App\Http\Controllers\TblpresensiController::class, 'update']);
    Route::get('/presensi/{id}', [App\Http\Controllers\TblpresensiController::class, 'show']);
    
    Route::post('/createPenitip', [App\Http\Controllers\TblpenitipController::class, 'createPenitip']);
    Route::put('/updatePenitip/{id}', [App\Http\Controllers\TblpenitipController::class, 'updatePenitip']);
    Route::delete('/deletePenitip/{id}', [App\Http\Controllers\TblpenitipController::class, 'deletePenitip']);
    Route::get('/searchPenitipByID/{id}', [App\Http\Controllers\TblpenitipController::class, 'searchPenitipById']);
    Route::post('/searchPenitipByNama', [App\Http\Controllers\TblpenitipController::class, 'searchPenitipByNama']);
    Route::post('/productForSpesificPenitip', [App\Http\Controllers\TblpenitipController::class, 'getAllProductByPenitip']);

    Route::get('/getTransactionToday', [App\Http\Controllers\TbltransaksiController::class, 'listofTransactionToday']);
    Route::get('/getTransactionStatusPayValid', [App\Http\Controllers\TbltransaksiController::class, 'listofTransactionStatusPembayaranValid']);
    Route::put('/MOAcceptTransaction/{id}', [App\Http\Controllers\TbltransaksiController::class, 'MOAcceptTransaction']);
    Route::put('/MORejectTransaction/{id}', [App\Http\Controllers\TbltransaksiController::class, 'MORejectTransaction']);
    Route::get('/getAllIngredientsAndProduct/{id}', [App\Http\Controllers\TbltransaksiController::class, 'getAllIngredientsAndProduct']);
    Route::get('/ingredients-product/{id}', [App\Http\Controllers\TbltransaksiController::class, 'getIngredientsOfProduct']);

    Route::put('/processing-product/{id}', [App\Http\Controllers\TbltransaksiController::class, 'MOChangeToDiproses']);
    Route::get('/getTransactionAccepted', [App\Http\Controllers\TbltransaksiController::class, 'ProcessingTransaction']);

    Route::get('/penggunaan-bahanbaku', [App\Http\Controllers\TblpenggunaanbahanbakuController::class, 'index']);

    
    Route::get('/laporan-penjualan-bulanan/{bulan}/{tahun}', [App\Http\Controllers\TbltransaksiController::class, 'laporanPenjualanBulanan']);
    Route::get('/laporan-penggunaan-bahan-baku/{tglAwal}/{tglAkhir}', [App\Http\Controllers\TblpenggunaanbahanbakuController::class, 'LaporanPenggunaanBahanBaku']);
    Route::get('/laporan-penjualan-tahunan/{tahun}', [App\Http\Controllers\TbltransaksiController::class, 'LaporanPenjualanTahunan']);

    Route::post('/laporanPresensiKaryawan', [App\Http\Controllers\LaporanController::class, 'getLaporanPresensi']);
    Route::post('/laporanPresensiKaryawanByBulanTahun', [App\Http\Controllers\LaporanController::class, 'getLaporanPresensiByBulanTahun']);
    Route::post('/laporanPemasukanPengeluaran', [App\Http\Controllers\LaporanController::class, 'getLaporanPemasukanPengeluaranBulanan']);
    Route::post('/laporanPemasukanPengeluaranByBulanTahun', [App\Http\Controllers\LaporanController::class, 'rekapTransaksiPenitipBulan']);
    Route::post('/laporanPenitipan', [App\Http\Controllers\LaporanController::class, 'rekapTransaksiPenitipBulan']);
});

Route::middleware(['auth:api-customer', 'role:Customer'])->group(function () {
    //rute yang cuma bisa diakses customer
    route::get('/customer/transaksi-selesai', [App\Http\Controllers\TbltransaksiController::class, 'ShowTransaksiSelesai']);
    route::put('/customer/confirm-transaksi-selesai/{id}', [App\Http\Controllers\TbltransaksiController::class, 'UpdateTransaksiSelesaiCustomer']);
    route::get('/customer/detail-transaksi/{id}', [App\Http\Controllers\TbldetailtransaksiController::class, 'ShowDetailTransaksi']);
    Route::get('/produk-customer', [App\Http\Controllers\TblprodukController::class, 'index']);
    Route::get('/produk/on-date/{date}', [App\Http\Controllers\TblprodukController::class, 'showProductByTglAmbil']);
    Route::get('/produk-customer/{id}', [App\Http\Controllers\TblprodukController::class, 'show']);

    // Mengurangi stok dan kuota berdasarkan id transaksinya (Not Done)
    Route::post('/reduce-stok/{id_trans}', [App\Http\Controllers\TblprodukController::class, 'reduceStok']);
    Route::post('/reduce-stok-ready/{id_trans}', [App\Http\Controllers\TblprodukController::class, 'reduceReady']);

    Route::get('/customer/history', [App\Http\Controllers\TbltransaksiController::class, 'getTransaksiCustomer']);
    Route::get('/complete-order', [App\Http\Controllers\TbltransaksiController::class, 'getCompleteTransCust']);
    Route::get('/customer/history/{nama}', [App\Http\Controllers\TbltransaksiController::class, 'searchDataHistoryTransaksi']);
    
    Route::get('/detail-transaksi', [App\Http\Controllers\TbldetailtransaksiController::class, 'index']);
    Route::get('/transaksi', [App\Http\Controllers\TbltransaksiController::class, 'index']);
    Route::post('/transaksi', [App\Http\Controllers\TbltransaksiController::class, 'store']);
    Route::post('/transaksi-ready', [App\Http\Controllers\TbltransaksiController::class, 'storeReady']);

    Route::put('/reduce-poin', [App\Http\Controllers\TbltransaksiController::class, 'reducePoin']);
    Route::put('/update-poin', [App\Http\Controllers\TblcustomerController::class, 'updatePoin']);

    Route::get('/kategori', [App\Http\Controllers\TblkategoriController::class, 'getAllKategori']);

    Route::post('/logoutCustomer', [App\Http\Controllers\AuthController::class, 'logoutCustomer']);

    Route::get('/customer-alamat', [App\Http\Controllers\TblcustomerController::class, 'getAlamatUser']);

    Route::get('/customerUnPayed', [App\Http\Controllers\TblcustomerController::class, 'showAllNeedToPay']);
    Route::post('/sendImageForPaying', [App\Http\Controllers\TblcustomerController::class, 'sendImageForPaying']);

    Route::get('/getTransaksiByIdCustomer/{id}', [App\Http\Controllers\TbltransaksiController::class, 'getTransaksiByIdCustomer']);
    Route::post('/sendProofPayment', [App\Http\Controllers\TbltransaksiController::class, 'sendProofPayment']);

    Route::post('/customerSaldo', [App\Http\Controllers\TblhistorysaldoController::class, 'customerRequestSaldo']);
    Route::get('/customerSaldo', [App\Http\Controllers\TblhistorysaldoController::class, 'customerGetAllHistory']);
});

Route::get('/produk-today', [App\Http\Controllers\TblprodukController::class, 'showProductToday']);
Route::get('/detail-transaksi-bahan', [App\Http\Controllers\TbldetailtransaksibahanbakuController::class, 'index']);


// Temporary Seto
Route::get('/getBahanBakuAll', [App\Http\Controllers\TblbahanbakuController::class, 'index']);
Route::post('/createBahanBaku', [App\Http\Controllers\TblbahanbakuController::class, 'createBahanBaku']);
Route::put('/updateBahanBaku/{id}', [App\Http\Controllers\TblbahanbakuController::class, 'updateBahanBaku']);
Route::delete('/deleteBahanBaku/{id}', [App\Http\Controllers\TblbahanbakuController::class, 'deleteBahanBaku']);
Route::post('/searchBahanBakuByNama', [App\Http\Controllers\TblbahanbakuController::class, 'SearchBahanBakuByNama']);

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
    Route::put('/update-customer', [App\Http\Controllers\TblcustomerController::class, 'update']);
    Route::post('/customer', [App\Http\Controllers\TblcustomerController::class, 'updateProfile']);
    
    Route::post('/customer/transaksi', [App\Http\Controllers\TbltransaksiController::class, 'store']); // cmn testing buat show history

    Route::post('/customer/detail-transaksi', [App\Http\Controllers\TbldetailtransaksiController::class, 'store']); // cmn testing buat show history

    Route::get('/customer/alamat', [App\Http\Controllers\TblalamatController::class, 'index']);
    Route::post('/customer/alamat', [App\Http\Controllers\TblalamatController::class, 'store']);
});

Route::post('/uploadFotoCloud', [App\Http\Controllers\TblcustomerController::class, 'testUpload']);
Route::post('/deleteFotoCloud', [App\Http\Controllers\TblcustomerController::class, 'testDelete']);

Route::get('/getRandomProduct', [App\Http\Controllers\TblprodukController::class, 'pickRandomFourProduct']);