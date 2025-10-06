<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/schedule')->as('schedule.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'ScheduleController@index')->name('index');
    Route::get('/create/{loan_code?}', 'ScheduleController@create')->name('create');
    Route::post('/store', 'ScheduleController@store')->name('store');
    Route::get('/load_schedule/{loan_code?}', 'ScheduleController@load_schedule')->name('load_schedule');
    Route::post('/update', 'ScheduleController@update')->name('update');
    Route::post('/delete', 'ScheduleController@delete')->name('delete');

});