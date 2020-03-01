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


Route::get('raw/{ticker}', function (\Illuminate\Http\Request $request) {
    $companyName = $request->route('ticker');

    $requestBody = \GuzzleHttp\json_encode([
        'data' => [
            $companyName =>
                [
                    'revenue' => sprintf('QFS(%s,revenue,FY-19:FY)', $companyName)
                ]
        ]
    ]);

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

        return $response->getBody()->getContents();
    } catch (RequestException $exception) {
        return $exception->getResponse();
    }

});
