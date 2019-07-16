<?php

namespace Kionik\Caesar;

use Kionik\Caesar\Parsers\Parser;
use Kionik\Caesar\Parsers\ParsersStorage;

/**
 * Class XmlReader
 *
 * @package Kionik\ReactExcel\Xml
 * @property ParsersStorage<Parser> $parsers
 */
class StringReader extends Reader
{
    /**
     * Create readable stream. Add subscribers for chunk and end events.
     *
     * @param string $string
     */
    public function read($string): void
    {
        foreach ($this->parsers as $parser) {
            $parser->parse($string);
        }
        $this->emitEnd();
    }
}