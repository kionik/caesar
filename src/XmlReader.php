<?php

namespace Kionik\Caesar;

use Kionik\Caesar\Handlers\HandlerInterface;
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
     * @param HandlerInterface $handler
     */
    public function onTag(string $tag, callable $listener, ?HandlerInterface $handler = null): void
    {
        $searcher = new TagSearcher($tag);
        $searcher->onFind($listener);
        if ($handler) {
            $searcher->setHandler($handler);
        }

        $this->parsers()->add(new FileParser($searcher));
    }
}