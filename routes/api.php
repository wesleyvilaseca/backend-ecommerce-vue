<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DepartamentController;
use App\Http\Controllers\Admin\ProductController;
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
    //departamentos
    Route::get('admin-departament/search', [DepartamentController::class, 'search']);
    Route::get('admin-departament/all', [DepartamentController::class, 'index']);
    Route::post('admin-departament/get', [DepartamentController::class, 'getDepartament']);
    Route::post('admin-departament/save', [DepartamentController::class, 'save']);
    Route::post('admin-departament/update', [DepartamentController::class, 'update']);
    Route::post('admin-departament/delete', [DepartamentController::class, 'delete']);

    //produtos
    Route::post('admin-product/save', [ProductController::class, 'save']);

    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
