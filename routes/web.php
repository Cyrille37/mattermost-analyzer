<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group([
    /*'middleware' => ['auth']*/
], function ()
{
    Route::get('/home', 'HomeController@index')->name('home');
    
    Route::get('/tables/channels', 'TablesController@channels')->name('tables.channels');
    Route::get('/tables/channels/data', 'TablesController@channels_data')->name('tables.channels.data');

    Route::get('/tables/members', 'TablesController@members')->name('tables.members');
    Route::get('/tables/members/data', 'TablesController@members_data')->name('tables.members.data');
});

