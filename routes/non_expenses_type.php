<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/non_expenses_type')->as('non_expenses_type.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'NonExpensesTypeController@index')->name('index');
    Route::get('/create', 'NonExpensesTypeController@create')->name('create');
    Route::post('/store', 'NonExpensesTypeController@store')->name('store');
    Route::get('/edit/{non_expenses_type}', 'NonExpensesTypeController@edit')->name('edit');
    Route::put('/update/{non_expenses_type}', 'NonExpensesTypeController@update')->name('update');
    Route::delete('/destroy/{non_expenses_type}', 'NonExpensesTypeController@destroy')->name('destroy');
});
