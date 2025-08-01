<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/house_ownership')->as('house_ownership.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'HouseOwnerShipController@index')->name('index');
    Route::get('/create', 'HouseOwnerShipController@create')->name('create');
    Route::post('/store', 'HouseOwnerShipController@store')->name('store');
    Route::get('/edit/{house_ownership}', 'HouseOwnerShipController@edit')->name('edit');
    Route::put('/update/{house_ownership}', 'HouseOwnerShipController@update')->name('update');
    Route::delete('/destroy/{house_ownership}', 'HouseOwnerShipController@destroy')->name('destroy');
});
