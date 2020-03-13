<?php


namespace App\Business\Clients;


use Psr\Http\Message\ResponseInterface;

interface IQuickFSClient
{
    /**
     * @param array $body
     * @return ResponseInterface
     */
    public function batchRequest(array $body): ResponseInterface;
}
