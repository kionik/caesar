<?php

namespace Kionik\Caesar;

use Kionik\Caesar\Searchers\StringSearcher;

/**
 * Class XmlReader
 *
 * @package Kionik\ReactExcel\Xml
 * @method onFind(string $string, callable $listener)
 */
class StringReader extends Reader
{
    /**
     * @var string
     */
    protected $defaultSearcher = StringSearcher::class;

    /**
     * Create readable stream. Add subscribers for chunk and end events.
     *
     * @param string $string
     */
    public function read(string $string): void
    {
        foreach ($this->parsers as $parser) {
            $parser->parse($string);
        }
        $this->emitEnd();
    }
}