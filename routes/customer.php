<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/customer')->as('customer.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'CustomerController@index')->name('index');
    Route::get('/create', 'CustomerController@create')->name('create');
    Route::post('/store', 'CustomerController@store')->name('store');
    // Route::get('/edit/{customer}', 'CustomerController@edit')->name('edit');
    Route::put('/update/{id}', 'CustomerController@update')->name('update');
    Route::delete('/destroy/{customer}', 'CustomerController@destroy')->name('destroy');
    Route::put('{id}/work', [CustomerController::class, 'updateWork'])->name('work.store');
    Route::post('reference', [CustomerController::class, 'storeReference'])->name('reference.store');
    Route::get('{id}/edit', [CustomerController::class, 'edit'])->name('edit');
    Route::get('reference/{reference}', [CustomerController::class, 'destroyReference'])->name('reference.destroy');
});
