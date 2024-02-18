<?php

namespace ArdaGnsrn\DevDumper\Payloads;

use ArdaGnsrn\DevDumper\Dumper\CustomDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class LogPayload extends Payload
{
    public function getKey(): string
    {
        return 'log';
    }

    public function validation($content): bool
    {
        return true;
    }

    public function handle(...$content)
    {
        parent::handle(...$content);
        $this->content = $content;
    }

    public function getContent(): array
    {
        $contents = $this->content;
        return [
            'content' => array_map(function ($value) {
                $clonedValue = (new VarCloner())->cloneVar($value);
                $value = (new CustomDumper())->dump($clonedValue, true);
                if (substr($value, -1) === "\n") $value = substr($value, 0, -1);
                return $value;
            }, $contents),
        ];
    }
}