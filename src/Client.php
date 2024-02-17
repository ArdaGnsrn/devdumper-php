<?php

namespace ArdaGnsrn\DevDumper;

use ArdaGnsrn\DevDumper\Exceptions\ServerIsNotRunningException;

class Client
{
    protected $port;
    protected $host;
    protected $client;

    public function __construct(int $port = 65310, string $host = 'localhost')
    {
        $this->port = $port;
        $this->host = $host;

        $this->client = new \GuzzleHttp\Client([
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

    public function sendPayload(PayloadFactory $factory)
    {
        if (!$this->testConnection()) throw new ServerIsNotRunningException($this->host, $this->port);

        $this->client->post('/', [
            'json' => $factory->getValues()
        ]);
    }


}