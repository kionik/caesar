<?php

namespace Kionik\Caesar;

use Ds\Set;
use Evenement\EventEmitter;
use Kionik\Caesar\Handlers\HandlerInterface;
use Kionik\Caesar\Parsers\Parser;
use Kionik\Caesar\Searchers\Searcher;
use Kionik\Caesar\Searchers\SearcherInterface;
use React\EventLoop\LoopInterface;

/**
 * Class Reader
 *
 * @package Kionik\Caesar
 */
abstract class Reader extends EventEmitter implements ReaderInterface
{
    /**
     * @var Set
     */
    protected $parsers;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * Default searcher class.
     * Need for onFind event
     *
     * @var string
     */
    protected $defaultSearcher = Searcher::class;

    /**
     * Reader constructor.
     *
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->parsers = new Set();
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
     * @return Set
     */
    public function parsers(): Set
    {
        return $this->parsers;
    }

    /**
     * Add search pattern and subscribe $listener to find
     * event. $listener will called when searcher find something
     * by pattern
     *
     * @param  string  $pattern
     * @param  callable  $listener
     *
     * @return mixed|void
     */
    public function onFind(string $pattern, callable $listener): self
    {
        /** @var SearcherInterface $searcher */
        $searcher = new $this->defaultSearcher($pattern);
        $searcher->onFind($listener);
        $this->parsers()->add(new Parser($searcher));

        return $this;
    }

    /**
     * Add handler to last added searcher
     *
     * @param  HandlerInterface  $handler
     *
     * @return Reader
     * @throws \RuntimeException
     */
    public function handler(HandlerInterface $handler): self
    {
        if ($this->parsers->isEmpty()) {
            throw new \RuntimeException('Can\'t add handler to empty parser');
        }

        /** @var Parser $parser */
        $parser = $this->parsers->last();

        $searcherHandler = $parser->getSearcher()->getHandler();
        if ($searcherHandler !== null) {
            $searcherHandler->setNext($handler);
        } else {
            $parser->getSearcher()->setHandler($handler);
        }

        return $this;
    }
}