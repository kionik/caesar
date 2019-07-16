<?php

namespace Kionik\Caesar;

use React\Stream\ReadableResourceStream;

/**
 * Class FileReader
 *
 * @package Kionik\Caesar\Readers
 */
class FileReader extends Reader
{
    /**
     * Create readable stream. Add subscribers for chunk and end events.
     *
     * @param string $filePath
     */
    public function read(string $filePath): void
    {
        $stream = new ReadableResourceStream(fopen($filePath, 'rb'), $this->loop);
        $stream->on('data', [$this, 'parse']);
        $stream->on('end', function () { $this->emitEnd(); });
    }

    /**
     * Parse file by parsers
     *
     * @param string $xmlChunk
     */
    protected function parse(string $xmlChunk): void
    {
        foreach ($this->parsers as $parser) {
            $parser->parse($xmlChunk);
        }
    }
}