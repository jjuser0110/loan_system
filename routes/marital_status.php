<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/marital_status')->as('marital_status.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'MaritalStatusController@index')->name('index');
    Route::get('/create', 'MaritalStatusController@create')->name('create');
    Route::post('/store', 'MaritalStatusController@store')->name('store');
    Route::get('/edit/{marital_status}', 'MaritalStatusController@edit')->name('edit');
    Route::put('/update/{marital_status}', 'MaritalStatusController@update')->name('update');
    Route::delete('/destroy/{marital_status}', 'MaritalStatusController@destroy')->name('destroy');
});
