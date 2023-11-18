<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

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
        // $tables = Schema::getAllTables();
        // DB::connection('mysql')->table('Persons')->insert([
        //     'PersonID' => 1,
        //     'LastName' => 'testlast',
        //     'FirstName' => 'testfirst',
        //     'Address' => 'abc',
        //     'City' => 'LA',
        //     // add more columns and values as needed
        // ]);

        // dd($tables);
        dd(DB::connection("mysql")->table('Users')->get()->first());
        return "Database connection is working!";
    } catch (\Exception $e) {
        return "Failed to connect to the database. Please check your configuration. error:" . $e;
    }
});
