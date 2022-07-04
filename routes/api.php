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
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Auth
Route::prefix('auth')->group(function () {
	Route::post('signup', 'Api\Auth\AuthController@signup')->name('auth.signup');
	Route::post('login', 'Api\Auth\AuthController@login')->name('auth.login');
	Route::post('logout', 'Api\Auth\AuthController@logout')->middleware('auth:sanctum')->name('auth.logout');
	Route::get('user', 'Api\Auth\AuthController@getAuthenticatedUser')->middleware('auth:sanctum')->name('auth.user');

    Route::get('password/verify/{token}', 'Api\Auth\AuthController@validateTokenPasswordReset')->name('password.verify');
	Route::post('password/email', 'Api\Auth\AuthController@sendPasswordResetLinkEmail')->middleware('throttle:5,1')->name('password.email');
	Route::post('password/reset', 'Api\Auth\AuthController@resetPassword')->name('password.reset');
	
	Route::get('email/verify/{id}', 'Api\VerificationController@verify')->name('verification.verify');
    Route::get('email/resend', 'Api\VerificationController@resend')->middleware('auth:sanctum')->name('verification.resend');
});

// get()->middleware(['psychologist', 'patient', 'admin'])
Route::group([
    'prefix' => 'test',
    'namespace' => 'Api',
    // 'middleware' => 'auth:sanctum'
], function ($router) {
    Route::get('/', 'TestController@index');
    Route::delete('/{test_id}', 'TestController@destroy');
    Route::get('/show/{test_id?}', 'TestController@show');
    Route::put('/video/{test_id}', 'TestController@updateVideoCall');
    Route::put('/finish/{test_id}', 'TestController@finishTest');
    Route::post('/createTest', 'TestController@createTest');
    Route::post('/storePatientAnswers', 'TestController@storePatientAnswers');
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
    Route::put('/update/{id}', 'PatientController@update');
    Route::delete('/delete/{id}', 'PatientController@delete');
    Route::get('/test-dashboard/{patient_id}', 'PatientController@getTestDashboard');
    Route::get('/consult-dashboard/{patient_id}', 'PatientController@getConsultDashboard');
    Route::get('/journal-dashboard/{patient_id}', 'PatientController@getJournalByDate');
});

Route::group([
    'prefix' => 'psychologist',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'PsychologistController@index');
    Route::get('/show/{id}', 'PsychologistController@show');
    Route::post('/create', 'PsychologistController@create');
    Route::put('/update/{id}', 'PsychologistController@update');
    Route::delete('/delete/{id}', 'PsychologistController@delete');
    Route::get('/main-dashboard/{psychologist_id}', 'PsychologistController@getMainDashboard');
    Route::get('/patients/{psychologist_id}', 'PsychologistController@getPatientList');
});

Route::group([
    'prefix' => 'chatschedule',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'ChatScheduleController@index');
    Route::get('/show/{id}', 'ChatScheduleController@show');
    Route::get('/chat/{id}', 'ChatScheduleController@get_detail_chat');
    Route::post('/create', 'ChatScheduleController@create');
    Route::put('/update/{id}', 'ChatScheduleController@update');
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
    Route::put('/update/{id}', 'RelationController@update');
    Route::delete('/delete/{id}', 'RelationController@delete');
    Route::put('/finish/{id}', 'RelationController@finishRelation');
});

Route::group([
    'prefix' => 'journal',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'JournalController@index');
    Route::get('/show/{id}', 'JournalController@show');
    Route::post('/create', 'JournalController@create');
    Route::put('/update/{id}', 'JournalController@update');
    Route::delete('/delete/{id}', 'JournalController@delete');
});

Route::group([
    'prefix' => 'tokenreg',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'RegisterTokenController@index');
    Route::get('/show/{token}', 'RegisterTokenController@show');
    Route::post('/create', 'RegisterTokenController@create');
});

Route::group([
    'prefix' => 'guardian',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'GuardianController@index');
    Route::get('/show/{id}', 'GuardianController@show');
    Route::post('/create', 'GuardianController@store');
    Route::put('/update/{id}', 'GuardianController@update');
    Route::delete('/delete/{id}', 'GuardianController@delete');
});

Route::group([
    'prefix' => 'testtype',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'TestTypeController@index');
    Route::get('/questions', 'TestTypeController@getTestQuestions');
    Route::get('/show/{id}', 'TestTypeController@show');
    Route::post('/create', 'TestTypeController@store');
    Route::put('/update/{id}', 'TestTypeController@update');
    Route::delete('/delete/{id}', 'TestTypeController@delete');
});

Route::group([
    'prefix' => 'message',
    'middleware' => 'auth:sanctum'
], function ($router) {
    Route::get('/{user}', 'Api\Auth\MessageController@privateMessages');
    Route::post('/{user}', 'Api\Auth\MessageController@sendPrivateMessage');
});

Route::group([
    'prefix' => 'notification',
    'namespace' => 'Api',
], function ($router) {
    Route::get('/', 'NotificationController@index');
    Route::get('/show/{user_id}', 'NotificationController@show');
    Route::post('/create', 'NotificationController@store');
    Route::delete('/delete/{id}', 'NotificationController@delete');
});