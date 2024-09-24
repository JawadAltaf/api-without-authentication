<?php

use App\Http\Controllers\Api\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UserController::class)->group(function(){
    Route::post('user/create','store')->name('user.create.api');
    Route::get('user/get/{flag}','index')->name('user.get.api');
    Route::get('user/{id}','show')->name('specific.user.get.api');
    Route::delete('user/delete/{id}','destroy')->name('user.delete.api');
    Route::put('user/update/{id}','update')->name('user.update.api');
    Route::patch('change/password/{id}','changePassword')->name('user.change.password.api');
});
