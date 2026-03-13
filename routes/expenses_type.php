<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/expenses_type')->as('expenses_type.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'ExpensesTypeController@index')->name('index');
    Route::get('/create', 'ExpensesTypeController@create')->name('create');
    Route::post('/store', 'ExpensesTypeController@store')->name('store');
    Route::get('/edit/{expenses_type}', 'ExpensesTypeController@edit')->name('edit');
    Route::put('/update/{expenses_type}', 'ExpensesTypeController@update')->name('update');
    Route::delete('/destroy/{expenses_type}', 'ExpensesTypeController@destroy')->name('destroy');
});
