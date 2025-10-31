<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/payment_method')->as('payment_method.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'PaymentMethodController@index')->name('index');
    Route::get('/load_payment_method', 'PaymentMethodController@load_payment_method')->name('load_payment_method');
     Route::get('/logs', 'PaymentMethodController@logs')->name('logs');
    Route::get('/load_payment_method_logs', 'PaymentMethodController@load_payment_method_logs')->name('load_payment_method_logs');
    Route::post('/store', 'PaymentMethodController@store')->name('store');
    Route::post('/update', 'PaymentMethodController@update')->name('update');
    Route::post('/update_credit', 'PaymentMethodController@update_credit')->name('update_credit');
    Route::get('/search_payment_methods', 'PaymentMethodController@search_payment_methods')->name('search_payment_methods');
});