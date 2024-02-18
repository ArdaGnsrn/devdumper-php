<?php

use ArdaGnsrn\DevDumper\DevDumper;

if (!function_exists('ddump')) {
    function ddump(...$args)
    {
        $dumper = new DevDumper();
        $dumper->dump(...$args);
        exit(0);
    }
}