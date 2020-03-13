<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * {
 * "T:CA" : {
 * "market_capitalization" : 10,
 * "outstanding_shares": 10,
 * "revenue": [],
 * "operating_cash_flow": [],
 * "capital_expenditure": [],
 * "net_income" : []
 * }
 * }
 */

//const NET_INCOME = 'net_income';
//const REVENUE = 'revenue';

Route::get('cool', function () {
    Log::debug('An informational message.');
});

Route::get('query', 'QuickFSController@queryStatements');
