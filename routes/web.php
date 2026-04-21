<?php

use App\Http\Controllers\Web\KasirController;
use Illuminate\Support\Facades\Route;
use App\Filament\Pages\MonthlyTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/login', '/admin/login')->name('login');

Route::get('/home', [KasirController::class, 'home'])->middleware('auth')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::get('/products', [KasirController::class, 'products'])->name('kasir.products');
    Route::get('/reports', [KasirController::class, 'reports'])->name('kasir.reports');
    Route::get('/more', [KasirController::class, 'more'])->name('kasir.more');
    Route::get('/more/printer-bluetooth', [KasirController::class, 'printerBluetooth'])->name('kasir.printer-bluetooth');
    Route::post('/kasir', [KasirController::class, 'store'])->name('kasir.store');
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('kasir.logout');
});

Route::get('/reports/monthly/{month}', MonthlyTransactionDetail::class)
    ->name('filament.pages.monthly-transaction-detail');
