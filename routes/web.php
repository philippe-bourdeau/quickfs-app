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

use App\Business\Clients\IQuickFSClient;
use Illuminate\Http\Request;
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

Route::get('raw/{ticker}', function (Request $request) {
    $ticker = $request->route('ticker');

    $body =[
            'data' => [
                'period_end_date' => sprintf('QFS(%s,period_end_date,FY-9:FY)', $ticker),
                'revenue' => sprintf('QFS(%s,revenue,FY-9:FY)', $ticker),
                'operating_cash_flow' => sprintf('QFS(%s,cf_cfo,FY-9:FY)', $ticker),
                'capex' => sprintf('QFS(%s,capex,FY-9:FY)', $ticker),
                'net_income' => sprintf('QFS(%s,net_income,FY-9:FY)', $ticker),
                'equity' => sprintf('QFS(%s,total_equity,FY-9:FY)', $ticker),
                'eps' => sprintf('QFS(%s,eps_diluted,FY-9:FY)', $ticker),
                'price' =>sprintf('QFS(%s,price)', $ticker),
                'market_cap' => sprintf('QFS(%s,mkt_cap)', $ticker)
            ]
        ];

    /** @var IQuickFSClient $client */
    $client = app(IQuickFSClient::class);
    $response = $client->batchRequest($body);
    $responseBody = \GuzzleHttp\json_decode($response->getBody()->getContents());
    $data = $responseBody->data;

    $statements = collect();
    for ($i= 0; $i<10;$i++ ) {
        $container = [
            'fiscal_end_date' => array_pop($data->period_end_date),
            'cash_flow_statement' => [
                'operating_cash_flow' => array_pop($data->operating_cash_flow),
                'capex' => array_pop($data->capex)
            ],
            'income_statement' => [
                'revenue' => array_pop($data->revenue),
                'net_income' => array_pop($data->net_income),
                'earnings_per_share' => array_pop($data->eps)
            ],
            'balance_sheet' => [
                'equity' => array_pop($data->equity)
            ]
        ];

        $statements->prepend($container);
    }

    $data = [
        'metadata' => [
            'ticker' => $ticker,
            'market_cap' => $data->market_cap,
            'price' => $data->price,
        ],
        'statements' => $statements->all()
    ];

    return \GuzzleHttp\json_encode($data);
});
