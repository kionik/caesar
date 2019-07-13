<?php

namespace Kionik\ReactXml;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

class Loop
{
    /**
     * @var LoopInterface
     */
    private static $_instance;

    /**
     * @return LoopInterface
     */
    public static function instance()
    {
        if (self::$_instance === null) {
            self::$_instance = Factory::create();
        }

        return self::$_instance;
    }

    private function __construct() {}
    private function __clone() {}
}