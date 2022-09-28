<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* ------------------ User Routes Start ---------------- */
Route::post('/addUser', [UserController::class, 'addUser']);
Route::post('/delUser/{id}', [UserController::class, 'delUser']);
Route::post('/loginUser', [UserController::class, 'loginUser']);
Route::get('/getUsers', [UserController::class, 'getUsers']);
Route::post('/updateUser/{user_id}', [UserController::class, 'updateUser']);
/* ------------------ User Routes End ---------------- */

/* ------------------ Category Routes Start ---------------- */
Route::post('/addCategory', [CategoryController::class, 'addCategory']);
Route::post('/delCategory/{id}', [CategoryController::class, 'deleteCategory']);
/* ------------------ Category Routes End ---------------- */