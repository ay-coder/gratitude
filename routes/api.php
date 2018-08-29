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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api',], function () 
{
    Route::post('login', 'UsersController@login')->name('api.login');
    Route::post('register', 'UsersController@create')->name('api.register');

    Route::post('social-register', 'UsersController@socialCreate')->name('api.social-register');

    Route::post('social-login', 'UsersController@socialLogin')->name('api.login');

    Route::post('validate-user', 'UsersController@validateUser')->name('api.validate-user');
    Route::post('forgotpassword', 'UsersController@forgotPassword')->name('api.forgotPassword');
    Route::post('user-profile', 'UsersController@getUserProfile')->name('api.user-profile');

    Route::get('categories', 'APICategoriesController@index')->name('categories.index');

    Route::get('config', 'UsersController@config')->name('api.config');

    Route::get('test-push-notification', 'UsersController@testNotification')->name('api.test-notification');
    /*Route::post('verifyotp', 'UsersController@verifyOtp')->name('api.verifyotp');
    Route::post('resendotp', 'UsersController@resendOtp')->name('api.resendotp');
    Route::post('forgotpassword', 'UsersController@forgotPassword')->name('api.forgotPassword');
    Route::post('specializations', 'SpecializationController@specializationList')->name('api.specializationList');
    Route::post('removeotp', 'UsersController@removeOtp')->name('api.removeotp');*/
});

Route::group(['namespace' => 'Api', 'middleware' => 'jwt.customauth'], function () 
{
    Route::post('update-user-profile', 'UsersController@updageUserProfile')->name('api.update-user-profile');

    Route::post('change-password', 'UsersController@changePassword')->name('api.change-password');

    Route::get('logout', 'UsersController@logout')->name('api.logout');
});

Route::group(['middleware' => 'jwt.customauth'], function () 
{
    includeRouteFiles(__DIR__.'/Api/');
});