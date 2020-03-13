<?php


namespace App\Business\Clients;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class QuickFSClient implements IQuickFSClient
{
    /**
     * @var Client
     */
    private Client $client;

    public function __construct(string $authHeader, string $apiKey, string $baseURI)
    {
        $this->client = new Client([
            RequestOptions::HEADERS => [
                'Accept' => 'Application/json',
                'Content-Type' => 'Application/json',
                $authHeader => $apiKey,
            ],
            'base_uri' => $baseURI
        ]);
    }

    /**
     * @param array $body
     * @return ResponseInterface
     */
    public function batchRequest(array $body): ResponseInterface
    {
        $request = new Request(
            'POST',
            'v1/data/batch',
            [],
            \GuzzleHttp\json_encode($body)
        );

        try {
            return $this->client->send($request);
        } catch (RequestException $exception) {
            return $exception->getResponse();
        }
    }
}
