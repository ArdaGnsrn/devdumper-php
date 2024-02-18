<?php

namespace ArdaGnsrn\DevDumper\Payloads;

use ArdaGnsrn\DevDumper\Exceptions\PayloadValidationException;

abstract class Payload
{
    protected $content;

    public function __construct(...$value)
    {
        $this->handle(...$value);
    }

    public static function create(...$value): self
    {
        return new static(...$value);
    }

    abstract public function getKey(): string;

    abstract public function validation($content): bool;

    abstract public function getContent(): array;

    public function getExceptionMessage($content): string
    {
        return 'Validation failed for value: ' . $content;
    }

    public function handle(...$content)
    {
        if (!$this->validation($content)) {
            throw new PayloadValidationException(
                sprintf("[%s] %s", self::class, $this->getExceptionMessage($content))
            );
        }

        $this->content = $content;
    }
}