<?php

namespace Kionik\Caesar;

use Kionik\Caesar\Parsers\FileParser;
use Kionik\Caesar\Searchers\TagSearcher;

/**
 * Class XmlReader
 *
 * @package Kionik\Caesar
 */
class XmlReader extends FileReader
{
    /**
     * Describe on 'find' tag event
     *
     * @param string $tag
     * @param callable $listener
     */
    public function onTag(string $tag, callable $listener): void
    {
        $searcher = new TagSearcher($tag);
        $searcher->onFind($listener);
        $this->parsers()->add(new FileParser($searcher));
    }
}