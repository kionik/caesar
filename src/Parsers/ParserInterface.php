<?php

namespace Kionik\Caesar\Parsers;

use Evenement\EventEmitterInterface;
use Kionik\Caesar\Searchers\SearcherInterface;

/**
 * Interface ParserInterface
 *
 * @package Kionik\Caesar\Parsers
 */
interface ParserInterface extends EventEmitterInterface
{
    /**
     * Method should parse $subject, Find matches
     * and call listener by using sendTag method.
     *
     * @param string $subject
     *
     * @return void
     */
    public function parse(string $subject): void;

    /**
     * @param SearcherInterface $finder
     *
     * @return mixed
     */
    public function setSearcher(SearcherInterface $finder);
}