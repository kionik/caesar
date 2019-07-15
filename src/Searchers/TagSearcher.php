<?php

namespace Kionik\Caesar\Searchers;

/**
 * Class TagSearcher
 *
 * @package Kionik\Caesar\Searchers
 */
class TagSearcher extends Searcher
{
    /**
     * TagSearcher constructor.
     *
     * @param string $searchTag
     */
    public function __construct(string $searchTag)
    {
        $this->setPattern("/<\s*?{$searchTag}\b[^>]*>(.*?)<\/{$searchTag}\b[^>]*>/s");
    }
}