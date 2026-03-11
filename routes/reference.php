<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/reference')->as('reference.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'ReferenceController@index')->name('index');
    Route::get('/create', 'ReferenceController@create')->name('create');
    Route::post('/store', 'ReferenceController@store')->name('store');
    Route::get('/edit/{reference}', 'ReferenceController@edit')->name('edit');
    Route::post('/update/{reference}', 'ReferenceController@update')->name('update');
    Route::get('/destroy/{reference}', 'ReferenceController@destroy')->name('destroy');
});
