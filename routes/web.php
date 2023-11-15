<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
    try {
        DB::connection('mysql')->getPdo();
        return "Database connection is working!";
    } catch (\Exception $e) {
        return "Failed to connect to the database. Please check your configuration. error:" . $e;
    }
});
Route::get('test', function () {
    return view('test');
});
Route::get('git', function () {
    return view('test');
});
Route::get('/db-test', function () {
    try {
        DB::connection('mysql')->getPdo();
        return "Database connection is working!";
    } catch (\Exception $e) {
        return "Failed to connect to the database. Please check your configuration. error:" . $e;
    }
});
