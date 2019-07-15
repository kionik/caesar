<?php

namespace Kionik\Caesar\Searchers;

/**
 * Class StringSearcher
 *
 * @package Kionik\Caesar\Searchers
 */
class StringSearcher extends Searcher
{
    /**
     * StringSearcher constructor.
     *
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->setPattern("/{$string}/");
    }
}