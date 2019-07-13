<?php

namespace Kionik\ReactXml\Reader;

use Evenement\EventEmitterInterface;
use Kionik\ReactXml\Reader\Exceptions\XmlParseException;

/**
 * Interface XmlChunkParserInterface
 *
 * @package Kionik\ReactExcel\Xml
 */
interface XmlParserInterface extends EventEmitterInterface
{
    /**
     * Method should parse each xml chunk, Find matches tags
     * and call tag listener by using sendTag method.
     *
     * @param string $xmlChunk
     *
     * @return void
     *
     * @throws XmlParseException
     */
    public function parse(string $xmlChunk);

    /**
     * @param TagHandlerInterface $handler
     *
     * @return void
     */
    public function setHandler(TagHandlerInterface $handler);
}