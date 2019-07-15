<?php

namespace Kionik\Caesar;

use Evenement\EventEmitterInterface;
use Kionik\Caesar\Parsers\ParsersStorage;

/**
 * Interface ReaderInterface
 *
 * @package Kionik\Caesar
 */
interface ReaderInterface extends EventEmitterInterface
{
    /**
     * Method should read subject and parse it by
     * parsers. When read is finished call onEnd $listener
     *
     * @param string $subject
     *
     * @return mixed
     */
    public function read($subject);

    /**
     * Parsers for current reader subject
     *
     * @return ParsersStorage
     */
    public function parsers(): ParsersStorage;

    /**
     * Finish event
     *
     * @param callable $listener
     *
     * @return void
     */
    public function onEnd(callable $listener);

    /**
     * Method emit 'end' event
     *
     * @return void
     */
    public function emitEnd();
}