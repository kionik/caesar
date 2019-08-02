<?php

namespace Kionik\Tests\Caesar;

use Kionik\Caesar\Parsers\Parser;
use Kionik\Caesar\Searchers\TagSearcher;
use Kionik\Caesar\XmlFileReader;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use React\EventLoop\Factory;

class XmlFileReaderTest extends MockeryTestCase
{
    /**
     * Testing that created searcher object will be
     * instance of TagSearcher
     */
    public function testIsTagSearcherClass(): void
    {
        $reader = new XmlFileReader(Factory::create());
        $reader->onFind('/test/', static function () {});
        /** @var Parser $parser */
        $parser = $reader->parsers()->last();
        $this->assertEquals(TagSearcher::class, get_class($parser->getSearcher()));
    }
}