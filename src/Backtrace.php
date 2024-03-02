<?php

namespace ArdaGnsrn\DevDumper;

class Backtrace
{
    protected $backtrace;

    public function __construct()
    {
        $this->refresh();
        $this->filter();
    }

    protected function refresh()
    {
        $this->backtrace = debug_backtrace();
    }

    protected function filter()
    {
        $this->backtrace = array_filter($this->backtrace, function ($trace) {
            return (
                $trace['class'] !== __CLASS__ &&
                $trace['class'] !== DevDumper::class
            );
        });
    }

    public function getFile(): array
    {
        $backtrace = $this->backtrace[array_key_first($this->backtrace)];
        $exploded = explode(DIRECTORY_SEPARATOR, $backtrace['file']);
        return [
            "file" => end($exploded),
            "line" => $backtrace['line']
        ];
    }
}