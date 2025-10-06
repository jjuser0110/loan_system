<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/payment')->as('payment.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'PaymentController@index')->name('index');
    Route::get('/schedule/{loan_code?}', 'PaymentController@schedule')->name('schedule');
    Route::get('/load_payment/{loan_code?}', 'PaymentController@load_payment')->name('load_payment');
    Route::get('/load_payment_schedule/{loan_code?}', 'PaymentController@load_payment_schedule')->name('load_payment_schedule');
    Route::get('/create/{loan_code?}', 'PaymentController@create')->name('create');
    Route::get('/create_schedule/{loan_code?}', 'PaymentController@create_schedule')->name('create_schedule');
    Route::post('/store', 'PaymentController@store')->name('store');
    Route::post('/update', 'PaymentController@update')->name('update');
    Route::post('/delete', 'PaymentController@delete')->name('delete');
    Route::get('/search_customer', 'PaymentController@search_customer')->name('search_customer');
});