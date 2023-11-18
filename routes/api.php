<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Models\Users;
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
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/testget', function () {
    $username = "testuser";
    $user = Users::where('username', $username)->first();
    return json_encode(["query" => $user]);
    // Example usage in a controller or elsewhere
    // $firstUser = \App\Models\MyUser::first();
    // if ($firstUser) {
    //     // Successfully retrieved a record
    //     return json_encode(['persons' => $firstUser]);
    // } else {
    //     // No records found
    //     echo json_encode(['message' => "no"]);;
    // }
});
//Route::get('/posts', [ApiController::class, 'getPosts']);
