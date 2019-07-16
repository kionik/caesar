<?php

namespace Kionik\Caesar\Searchers;

use Evenement\EventEmitterInterface;
use Kionik\Caesar\Handlers\HandlerInterface;

/**
 * Interface SearcherInterface
 *
 * @package Kionik\Caesar\Searchers
 */
interface SearcherInterface extends EventEmitterInterface
{
    /**
     * Method subscribe listener on 'find' event
     *
     * @param callable $listener
     *
     * @return void
     */
    public function onFind(callable $listener): void;

    /**
     * Method should emit 'find' event
     *
     * @param string $searchable
     *
     * @return void
     */
    public function emitFind(string $searchable): void;

    /**
     * Method make search in subject by pattern and
     * if find $searchable, then call emitFind method
     *
     * @param string $subject
     *
     * @return mixed
     */
    public function search(string $subject);

    /**
     * Method should set search pattern
     *
     * @param string $searchPattern
     *
     * @return mixed
     */
    public function setPattern(string $searchPattern);

    /**
     * Method should return search pattern
     *
     * @return string
     */
    public function getPattern(): string;

    /**
     * @param HandlerInterface $handler
     *
     * @return void
     */
    public function setHandler(HandlerInterface $handler): void;
}