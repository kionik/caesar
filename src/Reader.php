<?php

namespace Kionik\Caesar;

use Evenement\EventEmitter;
use Kionik\Caesar\Parsers\ParsersStorage;
use React\EventLoop\LoopInterface;

/**
 * Class Reader
 *
 * @package Kionik\Caesar
 */
abstract class Reader extends EventEmitter implements ReaderInterface
{
    /**
     * @var ParsersStorage
     */
    protected $parsers;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * Reader constructor.
     *
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->parsers = new ParsersStorage();
        $this->loop = $loop;
    }

    /**
     * Describe on 'end' event
     *
     * @param callable $listener
     */
    public function onEnd(callable $listener): void
    {
        $this->on('end', $listener);
    }

    /**
     * Method call read end event
     */
    public function emitEnd(): void
    {
        $this->emit('end');
    }

    /**
     * @return ParsersStorage
     */
    public function parsers(): ParsersStorage
    {
        return $this->parsers;
    }

    /**
     * Start ReactPHP
     */
    protected function run(): void
    {
        $this->loop->run();
    }
}