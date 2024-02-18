<?php

use ArdaGnsrn\DevDumper\DevDumper;

if (!function_exists('ddump')) {
    /**
     * Dump
     *
     * @param ...$args
     * @return DevDumper
     */
    function ddump(...$args)
    {
        $dumper = new DevDumper();
        $dumper->dump(...$args);
        return $dumper;
    }
}
if (!function_exists('ddie')) {
    /**
     * Dump and Die
     *
     * @param ...$args
     * @return void
     */
    function ddie(...$args)
    {
        ddump(...$args)->die();
    }
}