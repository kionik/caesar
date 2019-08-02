<?php

namespace Kionik\Caesar;

use Kionik\Caesar\Searchers\TagSearcher;

/**
 * Class XmlFileReader
 *
 * @package Kionik\Caesar
 * @method onFind(string $tag, callable $listener) : XmlFileReader
 */
class XmlFileReader extends FileReader
{
    /**
     * @var string
     */
    protected $defaultSearcher = TagSearcher::class;
}