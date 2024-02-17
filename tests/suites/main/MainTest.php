<?php

use ArdaGnsrn\DevDumper\DevDumper;
use PHPUnit\Framework\TestCase;

/**
 *  Corresponding class to test YourClass class
 *
 *  For each class in your library, there should be a corresponding unit test
 *
 *  @author yourname
 */
class MainTest extends TestCase
{

    /**
     * Just check if the YourClass has no syntax error
     *
     * This is just a simple check to make sure your library has no syntax error. This helps you troubleshoot
     * any typo before you even use this library in a real project.
     *
     * @return void
     */
    public function testDumper()
    {
        $object = new DevDumper();
        $object->dump([
            "lorem" => "ipsum"
        ]);
    }
}