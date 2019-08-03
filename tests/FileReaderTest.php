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
    public function testRead(): void
    {
        $fileName = 'example.txt';
        file_put_contents($fileName, 'some text in file with test');
        $tester = \Mockery::mock(\stdClass::class)->makePartial();
        $tester->shouldReceive('testMethod')->once();
        $reader = new FileReader(Factory::create());
        $reader->onFind('/test/', [$tester, 'testMethod']);
        $reader->read(fopen($fileName, 'rb'));
        $reader->run();
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
        $reader = new FileReader(Factory::create());
        $reader->onFind('/test/', [$tester, 'testMethod']);
        $reader->read(fopen($fileName, 'rb'));
        $data[] = 'test before find';
        $this->assertCount(1, $data);
        $this->assertEquals('test before find', $data[0]);
        $reader->run();
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
        $reader = new FileReader(Factory::create());
        $reader->read('test text');
    }

    /**
     * Testing that created searcher object will be
     * instance of Searcher
     */
    public function testIsSearcherClass(): void
    {
        $reader = new FileReader(Factory::create());
        $reader->onFind('/test/', static function () {});
        /** @var Parser $parser */
        $parser = $reader->parsers()->last();
        $this->assertEquals(Searcher::class, get_class($parser->getSearcher()));
    }
}