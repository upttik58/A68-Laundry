<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\KelolaJenisLaundryController;
use App\Http\Controllers\KelolaPaketLaundryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderanStafController;
use App\Http\Controllers\OrderLangsungController;
use App\Http\Controllers\OrderPaketController;
use Illuminate\Support\Facades\Route;

// ADMINS
// ===========================================================================
// login
Route::get('/loginAdmin', [LoginController::class, 'loginAdmin']);

// dashboard
Route::get('/dashboardAdmin', [DashboardAdminController::class, 'index']);

// jenis laundry
Route::get('/jenisLaundry', [KelolaJenisLaundryController::class, 'index']);
Route::post('/jenisLaundry', [KelolaJenisLaundryController::class, 'store']);
Route::post('/jenisLaundry/{id}', [KelolaJenisLaundryController::class, 'destroy']);
Route::post('/jenisLaundry/edit/{id}', [KelolaJenisLaundryController::class, 'update']);

// paket laundry
Route::get('/paketLaundry', [KelolaPaketLaundryController::class, 'index']);
Route::post('/paketLaundry', [KelolaPaketLaundryController::class, 'store']);
Route::post('/paketLaundry/{id}', [KelolaPaketLaundryController::class, 'destroy']);
Route::post('/paketLaundry/edit/{id}', [KelolaPaketLaundryController::class, 'update']);

// STAF LAUNDRY 
// ===========================================================================
// login
Route::get('/loginStaf', [LoginController::class, 'loginStaf']);

// orderan
Route::get('/offline', [OrderanStafController::class, 'offline']);
Route::post('/offline', [OrderanStafController::class, 'storeOffline']);
Route::post('/offline/{id}', [OrderanStafController::class, 'destroyOffline']);
Route::post('/offline/edit/{id}', [OrderanStafController::class, 'updateOffline']);
Route::post('/offline/bayar/{id}', [OrderanStafController::class, 'bayarOffline']);
Route::get('/offline/bayar/success/{id}', [OrderanStafController::class, 'bayarOfflineSuccess']);
Route::post('/offline/selesai/{id}', [OrderanStafController::class, 'bayarOfflineSelesai']);
Route::post('/offline/diambil/{id}', [OrderanStafController::class, 'bayarOfflineDiambil']);

Route::get('/online', [OrderanStafController::class, 'online']);
Route::post('/online/jemput/{id}', [OrderanStafController::class, 'jemput']);
Route::post('/online/berat', [OrderanStafController::class, 'berat']);
Route::post('/online/antar/{id}', [OrderanStafController::class, 'antar']);


// CUSTOMER 
// ===========================================================================
Route::get('/', [CustomerController::class, 'index']);

// register
Route::get('/registerCustomer', [LoginController::class, 'registerCustomer']);
Route::post('/registerCustomerStore', [LoginController::class, 'registerCustomerStore']);

// login
Route::get('/loginCustomer', [LoginController::class, 'loginCustomer']);
Route::post('/loginCustomerAuth', [LoginController::class, 'loginCustomerAuth']);

// status cucian
Route::post('/cekStatusCucian', [CustomerController::class, 'cekStatusCucian']);


// MEMBER 
// ===========================================================================
// paket laundry 
Route::get('/paketLaundryMember', [MemberController::class, 'index']);
Route::post('/paketLaundryMember', [MemberController::class, 'store']);
Route::post('/paketLaundryMember/{id}', [MemberController::class, 'destroy']);
Route::post('/paketLaundryMember/edit/{id}', [MemberController::class, 'update']);
Route::get('/paketLaundryMember/bayar/success/{id}', [MemberController::class, 'bayarSuccess']);

// order langsung
Route::post('/orderLangsung', [OrderLangsungController::class, 'orderLangsungStore']);
Route::post('/orderLangsung/edit/{id}', [OrderLangsungController::class, 'orderLangsungUpdate']);
Route::post('/orderLangsung/{id}', [OrderLangsungController::class, 'orderLangsungDestroy']);
Route::post('/bayarOrderan', [OrderLangsungController::class, 'orderLangsungBayar']);
Route::get('/orderLangsung/bayar/success/{id}', [OrderLangsungController::class, 'orderLangsungBayarSuccess']);
Route::get('/orderLangsung/setLocation/{id}', [OrderLangsungController::class, 'setLocation']);
Route::get('/orderLangsung/geocode', [OrderLangsungController::class, 'search']);
Route::post('/updateLocation', [OrderLangsungController::class, 'setLocationInsertOrUpdate']);
Route::post('/orderLangsung/selesai/{id}', [OrderLangsungController::class, 'selesai']);

// order paket 
Route::get('/orderPaket', [OrderPaketController::class, 'orderPaket']);
