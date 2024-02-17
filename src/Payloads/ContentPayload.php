<?php

namespace ArdaGnsrn\DevDumper\Payloads;

use ArdaGnsrn\DevDumper\Dumper\CustomDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class ContentPayload extends BasePayload
{
    public function getKey(): string
    {
        return 'content';
    }

    public function validation($value): bool
    {
        return is_string($value) && strlen($value) > 0;
    }

    public function setValue($value)
    {
        $clonedValue = (new VarCloner())->cloneVar($value);
        $value = (new CustomDumper())->dump($clonedValue, true);
        if (substr($value, -1) === "\n") $value = substr($value, 0, -1);
        parent::setValue($value);
    }
}