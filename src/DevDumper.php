<?php

namespace ArdaGnsrn\DevDumper;

use ArdaGnsrn\DevDumper\Payloads\ColorPayload;
use ArdaGnsrn\DevDumper\Payloads\LogPayload;
use ArdaGnsrn\DevDumper\Payloads\Payload;
use ArdaGnsrn\DevDumper\Payloads\TypePayload;
use Ramsey\Uuid\Uuid;

class DevDumper
{
    protected $uuid;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
    }

    public function dump(...$variables): self
    {
        if (empty($variables)) throw new \InvalidArgumentException('You must provide at least one variable to dump.');

        $payload = LogPayload::create(...$variables);

        return $this->request($payload);
    }

    public function color(string $color): self
    {
        $payload = ColorPayload::create($color);

        return $this->request($payload);
    }

    public function die()
    {
        exit(0);
    }

    protected function request(Payload $payload): self
    {
        $request = new Request();
        $response = $request->send(array_merge([
            'uuid' => $this->uuid,
            'type' => $payload->getKey(),
            'meta' => [
                'php_version' => phpversion(),
                ...(new Backtrace())->getFile()
            ],
        ], $payload->getContent()));

        if ($response->getStatusCode() !== 200) throw new \RuntimeException('An error occurred while sending the request to the server.');

        return $this;
    }
}