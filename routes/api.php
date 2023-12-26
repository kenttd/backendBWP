<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
Route::post('/login', [AuthController::class, 'login']); // untuk login
Route::post('/logout', [AuthController::class, 'logout']); // logout mungkin ga kepake (pake hapus cookie)
Route::post('/register', [AuthController::class, 'register']); // untuk register
Route::get('/{username}', [UserController::class, 'getUserByUsername']); // buat cari user
Route::get('/post/{id}', [UserController::class, 'Post']); //buat home user, id dari cookie
Route::get('/search/{username}', [UserController::class, 'search']); // waktu search
Route::post('/quack', [UserController::class, 'quack']); // buat post tweet
Route::get('/getpost/{id}', [UserController::class, 'getPost']); // buat dapet post dari user (ga harus user yang lagi log in)
Route::get('/getBookmark/{id}', [UserController::class, 'getBookmark']);
Route::get('/getLike/{id}', [UserController::class, 'getLike']);

Route::post('/doLike', [UserController::class, 'doLike']);
Route::post('/doUnLike', [UserController::class, 'doUnLike']);
Route::post('/doBookmark', [UserController::class, 'doBookmark']);
Route::post('/doUnBookmark', [UserController::class, 'doUnBookmark']);
