<?php

namespace ArdaGnsrn\DevDumper\Payloads;

use ArdaGnsrn\DevDumper\Exceptions\PayloadValidationException;

abstract class BasePayload
{
    protected $value;

    public function __construct($value)
    {
        $this->setValue($value);
    }

    abstract public function getKey(): string;

    abstract public function validation($value): bool;

    public function getExceptionMessage($value): string
    {
        return 'Validation failed for value: ' . $value;
    }

    public function setValue($value)
    {
        if (!$this->validation($value)) {
            throw new PayloadValidationException(
                sprintf("[%s] %s", self::class, $this->getExceptionMessage($value))
            );
        }

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}