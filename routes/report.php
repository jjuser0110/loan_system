<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/report')->as('report.')->middleware(['auth'])->group(function() {
    Route::get('/daily_report', 'ReportController@daily_report')->name('daily_report');
    Route::get('/load_daily_reports', 'ReportController@load_daily_reports')->name('load_daily_reports');
    Route::get('/cash_book_report', 'ReportController@cash_book_report')->name('cash_book_report');
    Route::get('/load_cash_book_reports', 'ReportController@load_cash_book_reports')->name('load_cash_book_reports');
    Route::get('/cash_book_report_history', 'ReportController@cash_book_report_history')->name('cash_book_report_history');
    Route::get('/load_cash_book_report_history', 'ReportController@load_cash_book_report_history')->name('load_cash_book_report_history');
    Route::get('/customer_payment_report', 'ReportController@customer_payment_report')->name('customer_payment_report');
    Route::get('/load_customer_payment_report', 'ReportController@load_customer_payment_report')->name('load_customer_payment_report');
    Route::get('/loan_list', 'ReportController@loan_list')->name('loan_list');
    Route::get('/load_loan_list', 'ReportController@load_loan_list')->name('load_loan_list');
    Route::get('/loan_list_pdf', 'ReportController@loan_list_pdf')->name('loan_list_pdf');
});