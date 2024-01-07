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
Route::get('/post/{id}', [UserController::class, 'Post']); //buat home user, id dari cookie
Route::get('/search/{username}', [UserController::class, 'search']); // waktu search
Route::post('/quack', [UserController::class, 'quack']); // buat post tweet
Route::get('/getpost/{id}/{requester}', [UserController::class, 'getPost']); // buat dapet post dari user
Route::get('/getBookmark/{id}', [UserController::class, 'getBookmark']);
Route::get('/getLike/{id}', [UserController::class, 'getLike']);
Route::post('/doLike', [UserController::class, 'doLike']);
Route::post('/doUnLike', [UserController::class, 'doUnLike']);
Route::post('/doRetweet', [UserController::class, 'doRetweet']);
Route::post('/doUnRetweet', [UserController::class, 'doUnRetweet']);
Route::post('/doBookmark', [UserController::class, 'doBookmark']);
Route::post('/doUnBookmark', [UserController::class, 'doUnBookmark']);
Route::post('/doFollow', [UserController::class, 'doFollow']);
Route::post('/doUnfollow', [UserController::class, 'doUnfollow']);
Route::get('/getuser', [AuthController::class, 'getuser']);
Route::post('/getMessages', [UserController::class, 'getMessages']);
Route::post('/doVerify', [UserController::class, 'doVerify']);
Route::post('/doBan', [UserController::class, 'doBan']);
Route::post('/doUnverify', [UserController::class, 'doUnverify']);
Route::post('/doUnban', [UserController::class, 'doUnban']);
Route::post('/doStaff', [UserController::class, 'doStaff']);
Route::post('/doUnstaff', [UserController::class, 'doUnstaff']);
Route::get('/getVerifiedPost/{id}/{role}', [UserController::class, 'getVerifiedPost'])->middleware('CheckRole:verified');
Route::post('/getMessagesSpecific', [UserController::class, 'getMessagesSpecific']);
Route::post('/sendMessage', [UserController::class, 'sendMessage']);
Route::post('/editMessage', [UserController::class, 'editMessage']);
Route::post('/deleteMessage', [UserController::class, 'deleteMessage']);
Route::get('/getTweetDetail/{TweetID}', [UserController::class, 'getTweetDetail']);
Route::get('/tweetExist/{TweetID}', [UserController::class, 'tweetExist']);
Route::get('/listFollowing/{Username}', [UserController::class, 'listFollowing']);
Route::get('/listFollower/{Username}', [UserController::class, 'listFollower']);
Route::get('/listVerifiedFollower/{Username}', [UserController::class, 'listVerifiedFollower']);

Route::get('/listLikes/{id}/{requester}', [UserController::class, 'listLikes']);
Route::get('/userExist/{username}', [UserController::class, 'userExist']);
Route::get('/searchTweet/{q}/{id}/{sort?}', [UserController::class, 'searchTweet']);
Route::post('/postReply', [UserController::class, 'doReply']);
Route::post('/editProfile', [UserController::class, 'editProfile']);
Route::post('/deleteTweet', [UserController::class, 'deleteTweet']);
Route::get('/getDeletedTweet/{id}', [UserController::class, 'getDeletetedTweet'])->middleware('CheckRole:verified');



Route::get('/{username}', [UserController::class, 'getUserByUsername']); // buat cari user
