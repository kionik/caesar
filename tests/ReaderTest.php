<?php

namespace Kionik\Tests\Caesar;

use Kionik\Caesar\Handlers\Handler;
use Kionik\Caesar\Parsers\Parser;
use Kionik\Caesar\Reader;
use Kionik\Caesar\Searchers\Searcher;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;
use React\EventLoop\Factory;

/**
 * Class ReaderTest
 *
 * @package Kionik\Tests\Caesar
 */
class ReaderTest extends MockeryTestCase
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var Mock
     */
    protected $testerMock;


    public function setUp(): void
    {
        $this->reader = new Reader(Factory::create());
        $this->testerMock = \Mockery::mock(\stdClass::class)->makePartial();
    }

    /**
     * Testing that Reader will subscribe some listener to Searcher find event
     */
    public function testOnFind(): void
    {
        $func = static function ($param) {};

        $this->reader->onFind('/test/', $func);
        /** @var Parser $parser */
        $parser = $this->reader->parsers()->first();
        $listeners = $parser->getSearcher()->listeners('find');

        $this->assertEquals($func, array_shift($listeners));
    }

    /**
     * Testing that method onEnd will call when reading is finished
     */
    public function testOnEnd(): void
    {
        $this->testerMock->shouldReceive('testMethod')->once();

        $this->reader->onEnd([$this->testerMock, 'testMethod']);
        $this->reader->read('some test text');
        $this->reader->run();
    }

    /**
     * Testing that reader will call listener when found some match and that it doesn't
     * call other listeners
     */
    public function testRead(): void
    {
        $this->testerMock->shouldReceive('testMethod')->once();
        $this->testerMock->shouldNotReceive('notFoundMethod');

        $this->reader->onFind('/test/', [$this->testerMock, 'testMethod']);
        $this->reader->onFind('/notFound/', [$this->testerMock, 'notFoundMethod']);

        $this->reader->read('some test text');
        $this->reader->run();
    }

    /**
     * Testing that method access only string types
     *
     * @param mixed $readParam
     *
     * @dataProvider readTypeProvider
     */
    public function testReadType($readParam): void
    {
        $this->expectException(\TypeError::class);
        $this->reader->read($readParam);
    }

    /**
     * @return array
     */
    public function readTypeProvider(): array
    {
        return [
            [123],
            [true],
            [null],
            [function () {}],
            [[]],
            [new \stdClass()],
        ];
    }

    /**
     * Testing that handler adds to searcher and
     * that if we call handler second time it will pass second handler
     * to first handler field $nextHandler
     */
    public function testHandler(): void
    {
        /** @var Handler|Mock $handler */
        $handler = \Mockery::mock(Handler::class)->makePartial();
        $this->reader
            ->onFind('/test/', function () {})
            ->handler($handler);

        /** @var Parser $parser */
        $parser = $this->reader->parsers()->last();
        $this->assertEquals($handler, $parser->getSearcher()->getHandler());

        $this->reader->handler($handler);

        $nextHandlerProp = new \ReflectionProperty(Handler::class, 'nextHandler');
        $nextHandlerProp->setAccessible(true);
        $result = $nextHandlerProp->getValue($handler);

        $this->assertEquals($handler, $result);
    }

    /**
     * Testing that if try to add handler without parser that will call exception
     */
    public function testHandlerWithoutParsers(): void
    {
        $this->expectException(\RuntimeException::class);

        /** @var Handler|Mock $handler */
        $handler = \Mockery::mock(Handler::class)->makePartial();
        $this->reader->handler($handler);
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
     * Testing that reading works asynchronously
     */
    public function testAsyncRead(): void
    {
        $data = [];

        $tester = \Mockery::mock(\stdClass::class)->makePartial();
        $tester
            ->shouldReceive('testMethod')
            ->once()
            ->andReturnUsing(function () use (&$data) {
                $data[] = 'test after find';
            });
        $reader = new Reader(Factory::create());
        $reader->onFind('/test/', [$tester, 'testMethod']);
        $reader->read('test text');
        $data[] = 'test before find';
        $this->assertCount(1, $data);
        $this->assertEquals('test before find', $data[0]);
        $reader->run();
        $this->assertCount(2, $data);
    }

}