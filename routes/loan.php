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
    Route::get('/fetch_outstanding/{loan_code?}', 'LoanController@fetch_outstanding')->name('fetch_outstanding');
    Route::get('/fetch_loan_amount/{loan_code?}', 'LoanController@fetch_loan_amount')->name('fetch_loan_amount');
    Route::get('/load_incoming_loan', 'LoanController@load_incoming_loan')->name('load_incoming_loan');
    Route::get('/load_overdue_loan', 'LoanController@load_overdue_loan')->name('load_overdue_loan');
    Route::get('/fetch_capital/{loan_code?}', 'LoanController@fetch_capital')->name('fetch_capital');
    Route::post('/delete', 'LoanController@delete')->name('delete');
});