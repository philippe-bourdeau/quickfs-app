<?php

namespace App\Http\Controllers;

use App\Business\Clients\IQuickFSClient;
use App\Http\Requests\StatementsReadRequest;
use Illuminate\Support\Str;

class QuickFSController extends Controller
{
    /**
     * @param StatementsReadRequest $request
     * @return string
     */
    public function queryStatements(StatementsReadRequest $request)
    {
        $ticker = Str::upper($request->route('ticker'));

        $redisEntry = \Illuminate\Support\Facades\Redis::get($ticker);
        if ($redisEntry) {
            return $redisEntry;
        }

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

        \Illuminate\Support\Facades\Redis::set(
            $ticker,
            \GuzzleHttp\json_encode($data),
            'EX',
            60 * 60 * 24
        );

        return \GuzzleHttp\json_encode($data);
    }
}
