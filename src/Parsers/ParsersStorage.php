<?php

namespace Kionik\Caesar\Parsers;

use Kionik\Utils\Interfaces\StorageInterface;
use Kionik\Utils\Traits\ArrayImitationTrait;

/**
 * Class ParserStorage
 *
 * @package Kionik\Caesar\Parsers
 * @method ParserInterface offsetGet($offset)
 * @method ParserInterface current()
 */
class ParsersStorage implements StorageInterface
{
    use ArrayImitationTrait;

    /**
     * @param ParserInterface $parser
     */
    public function add($parser)
    {
        $this->container[] = $parser;
    }

    /**
     * @param null $offset
     * @param ParserInterface $parser
     */
    public function offsetSet($offset, $parser): void
    {
        if ($offset !== null) {
            throw new \InvalidArgumentException('Offset must be null, ' . gettype($offset) . ' given');
        }

        if (!($parser instanceof ParserInterface)) {
            throw new \InvalidArgumentException('Parser must be instance of, ' . ParserInterface::class . '. ' . gettype($parser) . ' given');
        }

        $this->container[] = $parser;
    }
}