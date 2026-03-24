<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/customer')->as('customer.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'CustomerController@index')->name('index');
    Route::get('/fetch', 'CustomerController@fetch')->name('fetch');
    Route::get('/create', 'CustomerController@create')->name('create');
    Route::post('/store', 'CustomerController@store')->name('store');
    Route::put('/update/{id}', 'CustomerController@update')->name('update');
    Route::get('{customer}/edit', 'CustomerController@edit')->name('edit');

    // Route::post('/update/{customer}', 'CustomerController@update')->name('update');
    Route::post('/delete', 'CustomerController@delete')->name('delete');
    Route::put('{id}/work', 'CustomerController@updateWork')->name('work.store');
    
    // Reference routes
    Route::post('reference', 'CustomerController@storeReference')->name('reference.store');
    Route::get('reference/{reference}/edit', 'CustomerController@editReference')->name('reference.edit');
    Route::put('reference/{reference}', 'CustomerController@updateReference')->name('reference.update');
    Route::get('reference/{reference}', 'CustomerController@destroyReference')->name('reference.destroy');
    
    // Asset routes
    Route::post('asset/store', [CustomerController::class, 'storeAsset'])->name('asset.store');
    Route::get('asset/{asset}/edit', [CustomerController::class, 'editAsset'])->name('asset.edit');
    Route::put('asset/{asset}', [CustomerController::class, 'updateAsset'])->name('asset.update');
    Route::get('asset/{asset}', [CustomerController::class, 'destroyAsset'])->name('asset.destroy');

    Route::get('/single_customer', [CustomerController::class, 'single_customer'])->name('single_customer');
});