<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PembiayaanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/v1/pembiayaan', [PembiayaanController::class, 'index'])->name('list.pembiayaan');
Route::post('/v1/pembiayaan', [PembiayaanController::class, 'store'])->name('store.pembiayaan');
Route::get('/v1/pembiayaan/{id}', [PembiayaanController::class, 'show'])->name('show.pembiayaan');
