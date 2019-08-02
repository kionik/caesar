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
     * @param resource $resource
     * @param int|null $chunkSize
     */
    public function read($resource, ?int $chunkSize = null): void
    {
        $stream = new ReadableResourceStream($resource, $this->loop, $chunkSize);
        $stream->on('data', [$this, 'parse']);
        $stream->on('end', function () {
            $this->emitEnd();
        });
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