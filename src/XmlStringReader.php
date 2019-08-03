<?php

namespace Kionik\Caesar;

use Kionik\Caesar\Searchers\TagSearcher;

/**
 * Class XmlStringReader
 *
 * @package Kionik\Caesar
 */
class XmlStringReader extends Reader
{
    /**
     * @var string
     */
    protected $defaultSearcher = TagSearcher::class;

    /**
     * @param  string  $tag
     * @param  callable  $listener
     *
     * @return ReaderInterface
     */
    public function onFind(string $tag, callable $listener): ReaderInterface
    {
        return parent::onFind($tag, $listener);
    }
}