<?php

namespace ArdaGnsrn\DevDumper\Payloads;

class ColorPayload extends BasePayload
{
    const ACCEPTED_COLORS = [
        'green',
        'orange',
        'red',
        'purple',
        'blue',
        'gray'
    ];

    public function getKey(): string
    {
        return 'color';
    }

    public function validation($value): bool
    {
        return in_array($value, self::ACCEPTED_COLORS);
    }

    public function getExceptionMessage($value): string
    {
        return 'Validation failed for value: ' . $value . '. Accepted colors: ' . implode(', ', self::ACCEPTED_COLORS);
    }
}