<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/loan')->as('loan.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'LoanController@index')->name('index');
    Route::get('/load_loan/{customer_code?}', 'LoanController@load_loan')->name('load_loan');
    Route::get('/create/{company_code?}/{customer_code?}', 'LoanController@create')->name('create');
    Route::post('/store', 'LoanController@store')->name('store');
    Route::post('/calculate_loan', 'LoanController@calculate_loan')->name('calculate_loan');
    Route::post('/calculate_interest', 'LoanController@calculate_interest')->name('calculate_interest');
    Route::get('/search_customer', 'LoanController@search_customer')->name('search_customer');
    Route::get('/search_loan', 'LoanController@search_loan')->name('search_loan');
    Route::get('/single_loan/{loan_code?}', 'LoanController@single_loan')->name('single_loan');
    Route::get('/fetch_profit/{loan_code?}', 'LoanController@fetch_profit')->name('fetch_profit');
    Route::post('/delete', 'LoanController@delete')->name('delete');
});