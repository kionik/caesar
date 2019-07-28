<?php

namespace Kionik\Caesar;

use Kionik\Caesar\Searchers\TagSearcher;

/**
 * Class XmlReader
 *
 * @package Kionik\Caesar
 */
class XmlReader extends FileReader
{
    /**
     * @var string
     */
    protected $defaultSearcher = TagSearcher::class;

    /**
     * @param  string  $tag
     * @param  callable  $listener
     *
     * @return Reader
     */
    public function onFind(string $tag, callable $listener): Reader
    {
        return parent::onFind($tag, $listener);
    }
}