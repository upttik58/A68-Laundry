<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\KelolaJenisLaundryController;
use App\Http\Controllers\KelolaPaketLaundryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderanStafController;
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

Route::get('/online', [OrderanStafController::class, 'online']);


// CUSTOMER 
// ===========================================================================
// login
Route::get('/loginCustomer', [LoginController::class, 'loginCustomer']);

Route::get('/', [CustomerController::class, 'index']);
Route::get('/cekStatusCucian', [CustomerController::class, 'statusCucian']);


// MEMBER 
// ===========================================================================
Route::get('/member', [MemberController::class, 'index']);


// MIDTRANS 
// ===========================================================================
Route::POST('/midtrans/callback', [MidtransController::class, 'callback']);