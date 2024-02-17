<?php

namespace ArdaGnsrn\DevDumper;

use ArdaGnsrn\DevDumper\Payloads\BasePayload;

class PayloadFactory
{
    protected $values;

    public function __construct($values = [])
    {
        $this->values = $values;
    }

    public function setPayload(BasePayload $payload): self
    {
        $this->values[$payload->getKey()] = $payload->getValue();
        return $this;
    }

    public function getValues()
    {
        return array_merge($this->values, [
            'language' => 'php'
        ]);
    }
}