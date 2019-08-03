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
     * @var FileReader
     */
    protected $reader;

    public function setUp(): void
    {
        $this->reader = new FileReader(Factory::create());
    }

    /**
     * Testing that file correctly read by FileReader
     */
    public function testRead(): void
    {
        $fileName = 'example.txt';
        file_put_contents($fileName, 'some text in file with test');
        $tester = \Mockery::mock(\stdClass::class)->makePartial();
        $tester->shouldReceive('testMethod')->once();
        $this->reader->onFind('/test/', [$tester, 'testMethod']);
        $this->reader->read(fopen($fileName, 'rb'));
        $this->reader->run();
        unlink($fileName);
    }

    /**
     * Testing that reading works asynchronously
     */
    public function testAsyncRead(): void
    {
        $data = [];

        $fileName = 'example.txt';
        file_put_contents($fileName, 'some text in file with test');
        $tester = \Mockery::mock(\stdClass::class)->makePartial();
        $tester
            ->shouldReceive('testMethod')
            ->once()
            ->andReturnUsing(function () use (&$data) {
                $data[] = 'test after find';
            });
        $this->reader->onFind('/test/', [$tester, 'testMethod']);
        $this->reader->read(fopen($fileName, 'rb'));
        $data[] = 'test before find';
        $this->assertCount(1, $data);
        $this->assertEquals('test before find', $data[0]);
        $this->reader->run();
        $this->assertCount(2, $data);
        unlink($fileName);
    }

    /**
     * Testing that if we will pass not file object,
     * then reader throw exception
     */
    public function testNotFileRead(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->reader->read('test text');
    }

    /**
     * Testing that created searcher object will be
     * instance of Searcher
     */
    public function testIsSearcherClass(): void
    {
        $this->reader->onFind('/test/', static function () {});
        /** @var Parser $parser */
        $parser = $this->reader->parsers()->last();
        $this->assertEquals(Searcher::class, get_class($parser->getSearcher()));
    }

    /**
     * Testing that reader change parsers max store chunks count
     */
    public function testSetChunksStoreCount(): void
    {
        $this->reader->onFind('/test/', static function () {});
        $this->reader->setStoreChunksCount(10);
        /** @var Parser $parser */
        foreach ($this->reader->parsers() as $parser) {
            $this->assertEquals(10, $parser->getStoreChunksCount());
        }
    }
}