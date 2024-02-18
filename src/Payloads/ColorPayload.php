<?php

namespace ArdaGnsrn\DevDumper\Payloads;

class ColorPayload extends Payload
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

    public function validation($content): bool
    {
        print_r(gettype($content[0]));
        return !empty($content[0]) && in_array($content[0], self::ACCEPTED_COLORS);
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
            'color' => $contents[0]
        ];
    }
}