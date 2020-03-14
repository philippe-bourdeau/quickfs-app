<?php


namespace App\Business\Clients;


use Psr\Http\Message\ResponseInterface;

interface IQuickFSClient
{
    /**
     * @param array $body
     * @return ResponseInterface
     */
    public function multipleMetrics(array $body): ResponseInterface;

    public function singleMetric(string $ticker, string $metric, string $period): ResponseInterface;
}
