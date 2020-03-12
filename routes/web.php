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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
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

Route::get('cool', fn() => 'cool');

Route::get('raw/{ticker}', function (\Illuminate\Http\Request $request) {
    $ticker = $request->route('ticker');

    $requestBody = \GuzzleHttp\json_encode(
        [
            'data' => [
                'period_end_date' => sprintf('QFS(%s,period_end_date,FY-9:FY)', $ticker),
                'revenue' => sprintf('QFS(%s,revenue,FY-9:FY)', $ticker),
                'operating_cash_flow' => sprintf('QFS(%s,cf_cfo,FY-9:FY)', $ticker),
                'capex' => sprintf('QFS(%s,capex,FY-9:FY)', $ticker),
                'net_income' => sprintf('QFS(%s,net_income,FY-9:FY)', $ticker),
                'equity' => sprintf('QFS(%s,total_equity,FY-9:FY)', $ticker),
            ]
        ]
    );

    $request = new Request(
        'POST',
        '/v1/data/batch',
        [
            config('quickfs.auth-header') => config('quickfs.api-key'),
            'Content-Type' => 'application/json',
        ],
        $requestBody
    );

    $client = new Client([
        'base_uri' => config('quickfs.base-uri'),
    ]);

    try {
        $response = $client->send($request);
        $contents = $response->getBody()->getContents();
    } catch (RequestException $exception) {
        return $exception->getResponse();
    }

    $object = \GuzzleHttp\json_decode($contents);
    $data = $object->data;

    $statements = collect();
    for ($i= 0; $i<10;$i++ ) {
        $container = [
            'ticker' => $ticker,
            'year' => array_pop($data->period_end_date),
            'cash_flow_statement' => [
                'operating_cash_flow' => array_pop($data->operating_cash_flow),
                'capex' => array_pop($data->capex)
            ],
            'income_statement' => [
                'revenue' => array_pop($data->revenue),
                'net_income' => array_pop($data->net_income)
            ],
            'balance_sheet' => [
                'equity' => array_pop($data->equity)
            ]
        ];

        $statements->prepend($container);
    }

    return \GuzzleHttp\json_encode($statements->all());

});
