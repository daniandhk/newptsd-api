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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Auth
Route::prefix('auth')->group(function () {
	Route::post('signup', 'Api\Auth\AuthController@signup')->name('auth.signup');
	Route::post('login', 'Api\Auth\AuthController@login')->name('auth.login');
	Route::post('logout', 'Api\Auth\AuthController@logout')->middleware('auth:sanctum')->name('auth.logout');
	Route::get('user', 'Api\Auth\AuthController@getAuthenticatedUser')->middleware('auth:sanctum')->name('auth.user');

	Route::post('/password/email', 'Api\Auth\AuthController@sendPasswordResetLinkEmail')->middleware('throttle:5,1')->name('password.email');
	Route::post('/password/reset', 'Api\Auth\AuthController@resetPassword')->name('password.reset');
	
	Route::get('email/verify/{id}', 'Api\VerificationController@verify')->name('verification.verify');
    Route::get('email/resend', 'Api\VerificationController@resend')->middleware('auth:sanctum')->name('verification.resend');
});