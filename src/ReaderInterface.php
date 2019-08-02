<?php

namespace Kionik\Caesar;

use Ds\Set;
use Evenement\EventEmitterInterface;
use Kionik\Caesar\Handlers\HandlerInterface;

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
     * @param $subject
     *
     * @return mixed
     */
    public function read($subject);

    /**
     * Method should create Searcher by pattern.
     * Add to searcher onFind event current $listener.
     * Add this searcher into Parser and add it into parsers.
     *
     * @param  string  $pattern
     * @param  callable  $listener
     *
     * @return mixed
     */
    public function onFind(string $pattern, callable $listener);

    /**
     * Parsers for current reader subject
     *
     * @return Set
     */
    public function parsers(): Set;

    /**
     * Method should add handler to last added searcher.
     *
     * @param  HandlerInterface  $handler
     *
     * @return mixed
     */
    public function handler(HandlerInterface $handler);

    /**
     * Finish event
     *
     * @param callable $listener
     *
     * @return void
     */
    public function onEnd(callable $listener): void;

    /**
     * Method emit 'end' event
     *
     * @return void
     */
    public function emitEnd(): void;
}