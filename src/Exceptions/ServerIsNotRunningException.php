<?php

namespace ArdaGnsrn\DevDumper\Exceptions;

class ServerIsNotRunningException extends \Exception
{
    public static function make(string $host, int $port): self
    {
        return new static("DevDumper server is not running on {$host}:{$port}");
    }
}