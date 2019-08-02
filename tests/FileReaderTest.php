<?php

namespace Kionik\Tests\Caesar;

use Kionik\Caesar\FileReader;
use Kionik\Caesar\Parsers\Parser;
use Kionik\Caesar\Searchers\Searcher;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use React\EventLoop\Factory;

class FileReaderTest extends MockeryTestCase
{
    /**
     * Testing that file correctly read by FileReader
     */
    public function testRead()
    {
        // todo write method
        $this->assertTrue(true);
    }

    /**
     * Testing that if we will pass not file object,
     * then reader throw exception
     */
    public function testNotFileRead()
    {
        // todo write method
        $this->assertTrue(true);
    }

    /**
     * Testing that created searcher object will be
     * instance of Searcher
     */
    public function testIsSearcherClass()
    {
        $reader = new FileReader(Factory::create());
        $reader->onFind('/test/', static function () {});
        /** @var Parser $parser */
        $parser = $reader->parsers()->last();
        $this->assertEquals(Searcher::class, get_class($parser->getSearcher()));
    }
}