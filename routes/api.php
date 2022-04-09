<?php

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

// Set up 'cors' middleware for request methods (POST, GET etc.) and disable http request errors
Route::middleware('cors')->group(function () {
    Route::post('/client/register', 'ClientAPI\DeviceController@register')->middleware('device.register');
    Route::get('/client/purchase/{client_token}/{receipt}', 'ClientAPI\DeviceController@purchase');

    // ANDROID && IOS MOCK API
    Route::post('/v3/purchase/{receipt}', 'ClientAPI\DeviceController@mockApi');
});
