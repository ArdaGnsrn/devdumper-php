<?php

namespace ArdaGnsrn\DevDumper;

use ArdaGnsrn\DevDumper\Exceptions\ServerIsNotRunningException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Request
{
    protected $port;
    protected $host;
    protected $client;

    public function __construct(int $port = 65310, string $host = 'localhost')
    {
        $this->port = $port;
        $this->host = $host;

        $this->client = new Client([
            'base_uri' => "http://{$this->host}:{$this->port}",
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
        ]);
    }

    public function testConnection(): bool
    {
        try {
            $response = $this->client->get('/ping');
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function send($body): ResponseInterface
    {
        if (!$this->testConnection()) throw new ServerIsNotRunningException($this->host, $this->port);

        return $this->client->post('/', [
            'json' => $body
        ]);
    }
}