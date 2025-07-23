<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/cadmin')->as('cadmin.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'CadminController@index')->name('index');
    Route::get('/create', 'CadminController@create')->name('create');
    Route::post('/store', 'CadminController@store')->name('store');
    Route::get('/edit/{cadmin}', 'CadminController@edit')->name('edit');
    Route::post('/update/{cadmin}', 'CadminController@update')->name('update');
    Route::get('/destroy/{cadmin}', 'CadminController@destroy')->name('destroy');
});
