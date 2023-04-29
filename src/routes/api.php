<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Towoju5\AdminUserChat\AdminUserChat;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| 
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'api'], function () {
      Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'woju-chat'], function () {
            Route::get('/',               [AdminUserChat::class, 'sendMessageToUser']);
            Route::post('user/{id}',      [AdminUserChat::class, 'sendMessageToUser']);
            Route::post('all-admins',     [AdminUserChat::class, 'sendMessageToAllUsers']);
            Route::post('admin/{id}',     [AdminUserChat::class, 'sendMessageToAdministrator']);
            Route::post('all-users',      [AdminUserChat::class, 'sendMessageToAllAdministrators']);
      });
});
