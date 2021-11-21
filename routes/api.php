<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DepartamentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('refresh-token', [AuthController::class, 'refresh']);
Route::group(['middleware' => ['apiJwt']], function() {
    Route::get('admin-departament/search', [DepartamentController::class, 'search']);
    Route::get('admin-departament/all', [DepartamentController::class, 'index']);

    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
