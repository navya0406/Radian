<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/get_activity_list', 'RadianController@getActivityList');
Route::get('/issues', 'RadianController@issuesBySearch');
Route::get('/projects', 'RadianController@projectsBySearch');
Route::post('/add_time','RadianController@addTime');
Route::get('/get_time_entries','RadianController@getTimeEntries');
Route::put('/edit_time','RadianController@editTime');
Route::delete('/delete_time','RadianController@deleteTime');
Route::get('/login','RadianController@login');
