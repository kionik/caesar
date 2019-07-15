<?php

namespace Kionik\Caesar\Parsers;

use Evenement\EventEmitter;
use Kionik\Caesar\Searchers\SearcherInterface;

/**
 * Class Parser
 *
 * @package Kionik\Caesar\Parsers
 */
class Parser extends EventEmitter implements ParserInterface
{
    /**
     * @var SearcherInterface
     */
    protected $searcher;

    /**
     * Parser constructor.
     *
     * @param SearcherInterface $searcher
     */
    public function __construct(SearcherInterface $searcher)
    {
        $this->setSearcher($searcher);
    }

    /**
     * @param SearcherInterface $searcher
     *
     * @return void
     */
    public function setSearcher(SearcherInterface $searcher): void
    {
        $this->searcher = $searcher;
    }

    /**
     * @param string $subject
     */
    public function parse(string $subject): void
    {
        $this->searcher->search($subject);
    }
}