<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
 */
Route::group(['namespace' => 'Api'], function ()
{
    //Route for Preference Settings
    Route::get('get-preference-settings', 'LoginController@getPreferenceSettings');


});

/*
|--------------------------------------------------------------------------
| API Routes - With Authorization Middleware
|--------------------------------------------------------------------------
 */
Route::group(['namespace' => 'Api', 'middleware' => ['check-authorization-token']], function ()//bug-fixed-pm-v2.7
{
  
});

// Route::middleware(['auth:api', 'permission:manage_merchant']);//permission test
