<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/expense')->as('expense.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'ExpenseController@index')->name('index');
    Route::get('/load_expense/{loan_code?}', 'ExpenseController@load_expense')->name('load_expense');
    Route::post('/store', 'ExpenseController@store')->name('store');
    Route::post('/update', 'ExpenseController@update')->name('update');
    Route::post('/delete', 'ExpenseController@delete')->name('delete');
    Route::get('/fetch-expense', 'ExpenseController@fetch_expense')->name('fetch_expense');
});