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

Route::group([
    'prefix' => 'test',
    'namespace' => 'Api',
], function ($router) {
    Route::post('/', 'TestController@store');
    Route::get('/', 'TestController@index');
    Route::delete('/{test_id}', 'TestController@destroy');
    Route::get('/show/{test_id?}', 'TestController@show');
    Route::put('/video/{test_id}', 'TestController@updateVideoCall');
});

Route::group([
    'prefix' => 'consult',
    'namespace' => 'Api',
], function ($router) {
    Route::post('/', 'ConsultController@store');
    Route::get('/', 'ConsultController@index');
    Route::get('/show/{id}', 'ConsultController@show');

    Route::post('/question', 'ConsultController@storeNoteQuestion');
    Route::post('/answer', 'ConsultController@storeNoteAnswer');
});

Route::group([
    'prefix' => 'patient',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'PatientController@index');
    Route::get('/show/{id}', 'PatientController@show');
    Route::post('/create', 'PatientController@create');
    Route::post('/update/{id}', 'PatientController@update');
    Route::delete('/delete/{id}', 'PatientController@delete');
    Route::get('/dashboard/{user_id}', 'PatientController@getDashboard');
    Route::get('/journal-dashboard/{user_id}', 'PatientController@getJournalByDate');
});

Route::group([
    'prefix' => 'psychologist',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'PsychologistController@index');
    Route::get('/show/{id}', 'PsychologistController@show');
    Route::post('/create', 'PsychologistController@create');
    Route::post('/update/{id}', 'PsychologistController@update');
    Route::delete('/delete/{id}', 'PsychologistController@delete');
});

Route::group([
    'prefix' => 'chatschedule',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'ChatScheduleController@index');
    Route::get('/show/{id}', 'ChatScheduleController@show');
    Route::get('/chat/{id}', 'ChatScheduleController@get_detail_chat');
    Route::post('/create', 'ChatScheduleController@create');
    Route::post('/update/{id}', 'ChatScheduleController@update');
    Route::delete('/delete/{id}', 'ChatScheduleController@delete');
});

Route::group([
    'prefix' => 'relation',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'RelationController@index');
    Route::get('/show/{id}', 'RelationController@show');
    Route::get('/patient/{id}', 'RelationController@get_patient');
    Route::post('/create', 'RelationController@create');
    Route::post('/update/{id}', 'RelationController@update');
    Route::delete('/delete/{id}', 'RelationController@delete');
});

Route::group([
    'prefix' => 'journal',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'JournalController@index');
    Route::get('/show/{id}', 'JournalController@show');
    Route::post('/create', 'JournalController@create');
    Route::post('/update/{id}', 'JournalController@update');
    Route::delete('/delete/{id}', 'JournalController@delete');
});

Route::group([
    'prefix' => 'register',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'RegisterTokenController@index');
    Route::get('/show/{token}', 'RegisterTokenController@show');
    Route::post('/create', 'RegisterTokenController@create');
});