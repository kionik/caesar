<?php

namespace Kionik\ReactXml;

use Evenement\EventEmitter;
use Kionik\ReactXml\Reader\Exceptions\XmlParseException;
use Kionik\ReactXml\Reader\TagParser;
use Kionik\ReactXml\Reader\XmlParserInterface;
use Kionik\Utils\Traits\ArrayImitationTrait;
use React\Stream\ReadableResourceStream;

/**
 * Class XmlReader
 *
 * @package Kionik\ReactExcel\Xml
 */
class XmlReader extends EventEmitter implements \ArrayAccess, \Iterator
{
    use ArrayImitationTrait;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var XmlParserInterface[]
     */
    protected $parsers;

    /**
     * XmlReader constructor.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Create readable stream. Add subscribers for chunk and end events.
     */
    public function read(): void
    {
        $stream = new ReadableResourceStream(fopen($this->filePath, 'r'), Loop::instance());
        $stream->on('data', [$this, 'readChunk']);
        $stream->on('end', function () { $this->emit('end'); });
        Loop::instance()->run();
    }

    /**
     * Parse chunk item by parsers
     *
     * @param string $chunk
     *
     * @throws XmlParseException
     */
    public function readChunk(string $chunk): void
    {
        foreach ($this->parsers as $parser)
            $parser->parse($chunk);
    }

    /**
     * Add certain tag listener
     *
     * @param string $tag
     * @param callable $listener
     */
    public function onTag(string $tag, callable $listener): void
    {
        $parser = new TagParser($tag);
        $parser->on('find', $listener);
        $this[] = $parser;
    }

    /**
     * @return array
     */
    public function container(): array
    {
        return $this->parsers;
    }

    /**
     * @param int $offset
     * @param XmlParserInterface $value
     */
    public function offsetSet($offset, $value)
    {
        if (!($value instanceof XmlParserInterface))
            throw new \InvalidArgumentException('Value must be instance of ' . XmlParserInterface::class);

        $offset === null ? $this->parsers[] = $value : $this->parsers[$offset] = $value;
    }

    /**
     * @param $offset
     *
     * @return XmlParserInterface|null
     */
    public function offsetGet($offset): ?XmlParserInterface
    {
        if ($this->offsetExists($offset))
            return $this->parsers[$offset];

        return null;
    }

}