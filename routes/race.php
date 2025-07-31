<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/race')->as('race.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'RaceController@index')->name('index');
    Route::get('/create', 'RaceController@create')->name('create');
    Route::post('/store', 'RaceController@store')->name('store');
    Route::get('/edit/{race}', 'RaceController@edit')->name('edit');
    Route::put('/update/{race}', 'RaceController@update')->name('update');
    Route::delete('/destroy/{race}', 'RaceController@destroy')->name('destroy');
});
