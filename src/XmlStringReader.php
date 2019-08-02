<?php

namespace Kionik\Caesar;

use Kionik\Caesar\Searchers\TagSearcher;

/**
 * Class XmlStringReader
 *
 * @package Kionik\Caesar
 * @method onFind(string $tag, callable $listener) : XmlStringReader
 */
class XmlStringReader extends Reader
{
    /**
     * @var string
     */
    protected $defaultSearcher = TagSearcher::class;
}