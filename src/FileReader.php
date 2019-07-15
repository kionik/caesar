<?php

namespace Kionik\Caesar;

use Kionik\Caesar\Parsers\FileParser;
use Kionik\Caesar\Parsers\ParsersStorage;
use React\Stream\ReadableResourceStream;

/**
 * Class FileReader
 *
 * @package Kionik\Caesar\Readers
 * @property ParsersStorage<FileParser> $parsers
 */
class FileReader extends Reader
{
    /**
     * Create readable stream. Add subscribers for chunk and end events.
     *
     * @param string $filePath
     */
    public function read($filePath): void
    {
        $stream = new ReadableResourceStream(fopen($filePath, 'r'), $this->loop);
        $stream->on('data', [$this, 'parse']);
        $stream->on('end', function () { $this->emitEnd(); });
        $this->run();
    }

    /**
     * Parse file by parsers
     *
     * @param string $xmlChunk
     */
    protected function parse(string $xmlChunk)
    {
        foreach ($this->parsers as $parser)
            $parser->parse($xmlChunk);
    }
}