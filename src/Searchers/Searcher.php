<?php

namespace Kionik\Caesar\Searchers;

use Evenement\EventEmitter;

/**
 * Class Searcher
 *
 * @package Kionik\Caesar\Searchers
 */
class Searcher extends EventEmitter implements SearcherInterface
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @param callable $listener
     */
    public function onFind(callable $listener): void
    {
        $this->on('find', $listener);
    }

    /**
     * @param string $searchable
     */
    public function emitFind(string $searchable): void
    {
        $this->emit('find', [$searchable]);
    }

    /**
     * @param string $subject
     *
     * @return void
     */
    public function search(string $subject): void
    {
        if ($matches = $this->getMatches($subject))
            foreach ($matches as $match)
                $this->emitFind($match[0]);
    }

    /**
     * @param string $searchPattern
     */
    public function setPattern(string $searchPattern): void
    {
        $this->pattern = $searchPattern;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get matches in chunk by pattern
     *
     * @param string $subject
     *
     * @return array|null
     */
    protected function getMatches(string $subject): ?array
    {
        if (preg_match_all($this->pattern, $subject, $matches, PREG_SET_ORDER))
            return $matches;

        return null;
    }
}