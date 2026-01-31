<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/badmin')->as('badmin.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'BadminController@index')->name('index');
    Route::get('/create', 'BadminController@create')->name('create');
    Route::post('/store', 'BadminController@store')->name('store');
    Route::get('/edit/{badmin}', 'BadminController@edit')->name('edit');
    Route::post('/update/{badmin}', 'BadminController@update')->name('update');
    Route::get('/destroy/{badmin}', 'BadminController@destroy')->name('destroy');
});
