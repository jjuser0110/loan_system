<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/report')->as('report.')->middleware(['auth'])->group(function() {
    Route::get('/daily_report', 'ReportController@daily_report')->name('daily_report');
    Route::get('/load_daily_reports', 'ReportController@load_daily_reports')->name('load_daily_reports');
    Route::get('/cash_book_report', 'ReportController@cash_book_report')->name('cash_book_report');
    Route::get('/load_cash_book_reports', 'ReportController@load_cash_book_reports')->name('load_cash_book_reports');
});