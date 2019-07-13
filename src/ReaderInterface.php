<?php

namespace Kionik\ReactXml;

use Evenement\EventEmitterInterface;

interface ReaderInterface extends EventEmitterInterface
{
    /**
     * Method should create readable stream and
     * add subscribers for this stream. Rise 'end' event
     * on stream end. Start ReactPHP event loop.
     *
     * @return void
     */
    public function read();
}