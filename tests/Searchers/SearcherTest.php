<?php

namespace Kionik\Tests\Caesar\Searchers;

use Kionik\Caesar\Handlers\Handler;
use Kionik\Caesar\Handlers\HandlerInterface;
use Kionik\Caesar\Searchers\Searcher;
use Kionik\Caesar\Searchers\SearcherInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;
use PHPUnit\Framework\MockObject\MockObject;

class SearcherTest extends MockeryTestCase
{
    /**
     * @var Searcher
     */
    protected $searcher;

    /**
     * @var string
     */
    protected $searchable = 'test';

    public function setUp()
    {
        $this->searcher = new Searcher('/' . $this->searchable. '/');
    }

    /**
     * Testing that listener is subscribing on 'find' event.
     * Testing that if we subscribe new one listener on 'find' method
     * it will append to listeners
     * Testing that searcher will call each listener if it find something
     * Testing that searcher never call other listeners
     */
    public function testOnFind(): void
    {
        $listener1 = \Mockery::mock(\stdClass::class);
        $listener1->shouldReceive('wakeUp')->once();
        $this->searcher->onFind([$listener1, 'wakeUp']);
        $listeners = $this->searcher->listeners('find');
        $this->assertCount(1, $listeners);

        $listener2 = \Mockery::mock(\stdClass::class);
        $listener2->shouldReceive('wakeUp')->once();
        $this->searcher->onFind([$listener2, 'wakeUp']);
        $listeners = $this->searcher->listeners('find');
        $this->assertCount(2, $listeners);

        $listener3 = \Mockery::mock(\stdClass::class);
        $listener3->shouldNotReceive('wakeUp');
        $this->searcher->on('notFind', [$listener3, 'wakeUp']);
        $listeners = $this->searcher->listeners('find');
        $this->assertCount(2, $listeners);

        $this->searcher->emitFind('test');
    }

    /**
     * Testing that send searchable by emitFind is equal to first param in listener
     */
    public function testEmitFind(): void
    {
        $this->searcher->onFind(function ($param) {
            $this->assertEquals($this->searchable, $param);
        });
        $this->searcher->emitFind($this->searchable);
    }

    /**
     * Testing that if searcher has handler, then handler will
     * handle value and return a new result
     */
    public function testEmitFindWithHandler(): void
    {
        $newValue = 'new test';

        /** @var HandlerInterface|MockObject $handler */
        $handler = $this->createMock(Handler::class);
        $handler->expects($this->once())->method('handle')->willReturn($newValue);

        $this->searcher->setHandler($handler);
        $this->searcher->onFind(function ($param) use ($newValue) {
            $this->assertEquals($newValue, $param);
        });
        $this->searcher->emitFind($this->searchable);
    }

    /**
     * Testing that search method will find match and it will be correct
     *
     * @dataProvider searchProvider
     * @param string $text
     */
    public function testSearch(string $text): void
    {
        $pattern = $this->searcher->getPattern();
        $this->searcher->onFind(function ($param) use ($pattern) {
            $this->assertRegExp($pattern, $param);
            $this->assertEquals($this->searchable, $param);
        });
        $this->searcher->search($text);
    }

    /**
     * @return array
     */
    public function searchProvider(): array
    {
        return [
            ['test'],
            ['test text'],
            ['testnewtext']
        ];
    }

    /**
     * Testing that search method NOT found any matches
     *
     * @dataProvider notSearchProvider
     * @param string $text
     */
    public function testNotSearch(string $text): void
    {
        /** @var SearcherInterface|Mock $searcher */
        $searcher = \Mockery::mock(Searcher::class)->makePartial();
        $searcher->shouldNotReceive('emitFind');
        $searcher->setPattern('/' . $this->searchable . '/');
        $searcher->search($text);
    }

    /**
     * @return array
     */
    public function notSearchProvider(): array
    {
        return [
            ['tst'],
            ['tsted text, tes t'],
            ['t e s t'],
            ['t est'],
            ['te st'],
            ['string have no tst']
        ];
    }

    /**
     * Testing that search find all matches
     *
     * @dataProvider searchMatchesProvider
     * @param string $text
     * @param int $expectedCount
     */
    public function testSearchMatchesCount(string $text, int $expectedCount): void
    {
        /** @var SearcherInterface|Mock $searcher */
        $searcher = \Mockery::mock(Searcher::class)->makePartial();
        $searcher->shouldReceive('emitFind')->times($expectedCount);
        $searcher->setPattern('/' . $this->searchable . '/');
        $searcher->search($text);
    }

    /**
     * @return array
     */
    public function searchMatchesProvider(): array
    {
        return [
            ['tst testtest, is not test', 3],
            ['t e s t is one test and second test', 2],
            ['test somebody finaly', 1],
            ['testtesttest', 3],
            ['testest', 1],
            ['string have no tst', 0]
        ];
    }
}
