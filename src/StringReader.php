<?php

namespace Kionik\Caesar;

/**
 * Class XmlReader
 *
 * @package Kionik\ReactExcel\Xml
 */
class StringReader extends Reader
{
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