<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/customer')->as('customer.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'CustomerController@index')->name('index');
    Route::get('/create', 'CustomerController@create')->name('create');
    Route::post('/store', 'CustomerController@store')->name('store');
    Route::put('/update/{id}', 'CustomerController@update')->name('update');
    Route::get('{id}/edit', 'CustomerController@edit')->name('edit');
    Route::delete('/destroy/{customer}', 'CustomerController@destroy')->name('destroy');
    Route::put('{id}/work', 'CustomerController@updateWork')->name('work.store');
    Route::post('reference', 'CustomerController@storeReference')->name('reference.store');
    Route::get('reference/{reference}', 'CustomerController@destroyReference')->name('reference.destroy');
});
