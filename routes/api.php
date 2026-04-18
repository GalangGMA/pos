<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\AppVersionController;
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
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
Route::post('/success', function () {
    return response()->json(['message' => 'berhasil API']);
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login-mobile', [AuthController::class, 'loginx']);
Route::post('/login', function(Request $request) {
    if (Auth::attempt($request->only('username', 'password'))) {
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }
    return response()->json(['message' => 'Invalid credentials'], 401);
});

Route::get('/app-version/latest', [AppVersionController::class, 'latest']);
Route::get('/check-version', [AppVersionController::class, 'check']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/produk', [ProdukController::class, 'index']);
    Route::get('/produk/{id}', [ProdukController::class, 'show']);
    Route::post('/produk', [ProdukController::class, 'store']);
    Route::put('/produk/{id}', [ProdukController::class, 'update']);
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);
    Route::post('/produk/{id}/update-stok', [ProdukController::class, 'updateStok']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bank-accounts', [BankAccountController::class, 'index']);

    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/report', [TransaksiController::class, 'getDetailAll']);
    Route::get('/transaksi/type-bayar', [TransaksiController::class, 'typeBayar']);
    Route::get('/transaksi/{id}/detail', [TransaksiController::class, 'getDetail']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
});
