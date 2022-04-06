<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DebitController;
use App\Http\Controllers\PointLogController;
use App\Http\Controllers\CashController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home',[HomeController::class, 'index'])->name('home');

// Auth::routes();


Route::group(['middleware' => ['web','auth']], function () {
    // Route::get('/users', [App\Http\Controllers\HomeController::class, 'index']);

    
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

// Route::get('/log', function () 
//     {
//         return view('pages.pointlog');
//     })->name('pointlog');


Route::get('users', [UsersController::class, 'users'])->name('users');
Route::get('/users/{user}', [UsersController::class, 'usersInfo'])->name('users.info');
Route::post('/users/{user}/update', [UsersController::class, 'usersInfoUpdate'])->name('users.info.update');
Route::post('/code/regen', [UsersController::class, 'getNewUserPersonalCode'])->name('user.personal.new');


Route::get('credit', [CreditController::class, 'credit'])->name('credit');
Route::post('credit/confirm', [CreditController::class, 'confirm'])->name('credit.confirm');
Route::post('credit/cancel', [CreditController::class, 'cancel'])->name('credit.cancel');
Route::post('credit/restore/confirm', [CreditController::class, 'creditRestoreConfirm'])->name('credit.restore.confirm');
Route::post('credit/restore/cancel', [CreditController::class, 'creditRestoreCancel'])->name('credit.restore.cancel');


Route::get('debit', [DebitController::class, 'debit'])->name('debit');
Route::post('debit/confirm', [DebitController::class, 'confirm'])->name('debit.confirm');
Route::post('debit/cancel', [DebitController::class, 'cancel'])->name('debit.cancel');
Route::post('debit/restore/confirm', [DebitController::class, 'debitRestoreConfirm'])->name('debit.restore.confirm');
Route::post('debit/restore/cancel', [DebitController::class, 'debitRestoreCancel'])->name('debit.restore.cancel');


Route::post('/cash/change/{user}', [CashController::class, 'manualCashChange'])->name('manual.cash');


Route::get('log', [PointLogController::class, 'pointlog'])->name('pointlog');




// Route::get('/users/{user}', function () {
//     return view('users/info');
// });