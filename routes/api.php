<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['prefix' => 'v1'], function(){
    Route::Resource('meeting', 'MeetingController', [
        'except' => ['create','edit']
    ]);
    Route::Resource('meeting/registration', 'RegistrationController', [
        'only' => ['store','destroy']
    ]);
    Route::post('/user/register', [
        'uses' => 'AuthController@store'
    ]);
    Route::post('/user/signin', [
        'uses' => 'AuthController@signin'
    ]);
});

