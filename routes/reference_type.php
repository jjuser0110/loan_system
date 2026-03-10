<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/reference_type')->as('reference_type.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'ReferenceTypeController@index')->name('index');
    Route::get('/create', 'ReferenceTypeController@create')->name('create');
    Route::post('/store', 'ReferenceTypeController@store')->name('store');
    Route::get('/edit/{reference_type}', 'ReferenceTypeController@edit')->name('edit');
    Route::put('/update/{reference_type}', 'ReferenceTypeController@update')->name('update');
    Route::delete('/destroy/{reference_type}', 'ReferenceTypeController@destroy')->name('destroy');
});
