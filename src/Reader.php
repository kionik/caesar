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
class Reader extends EventEmitter implements ReaderInterface
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
     * Reads one string and try to find some matches
     * by parsers.
     *
     * @param  string  $string
     *
     * @return mixed|void
     */
    public function read($string)
    {
        if (is_string($string) !== true) {
            throw new \TypeError(
                sprintf('Parameter $string must be of type string, %s given', gettype($string))
            );
        }

        foreach ($this->parsers as $parser) {
            $parser->parse($string);
        }
        $this->emitEnd();
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
    public function onFind(string $pattern, callable $listener): ReaderInterface
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
            throw new \RuntimeException('Property parsers is empty. Can\'t add handler to empty parser');
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

    /**
     * Start event loop
     */
    public function run(): void
    {
        $this->loop->run();
    }
}