<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/employer_type')->as('employer_type.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'EmployerTypeController@index')->name('index');
    Route::get('/create', 'EmployerTypeController@create')->name('create');
    Route::post('/store', 'EmployerTypeController@store')->name('store');
    Route::get('/edit/{employer_type}', 'EmployerTypeController@edit')->name('edit');
    Route::put('/update/{employer_type}', 'EmployerTypeController@update')->name('update');
    Route::delete('/destroy/{employer_type}', 'EmployerTypeController@destroy')->name('destroy');
});
