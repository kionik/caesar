<?php

namespace Kionik\Tests\Caesar;

use Kionik\Caesar\Handlers\Handler;
use Kionik\Caesar\Handlers\HandlerInterface;
use Kionik\Caesar\Searchers\Searcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SearcherTest extends TestCase
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
        $this->searcher = new Searcher();
    }

    /**
     * Test listener subscribing on 'find' event
     */
    public function testOnFind(): void
    {
        $listener = function () {};
        $this->searcher->onFind($listener);
        $listeners = $this->searcher->listeners('find');
        $this->assertEquals($listener, array_pop($listeners));
    }

    /**
     * Test that send searchable is equal to first param in listener
     */
    public function testEmitFind(): void
    {
        $this->searcher->onFind(function ($param) {
            $this->assertEquals($this->searchable, $param);
        });
        $this->searcher->emitFind($this->searchable);
    }

    /**
     * Test that after handling value, searcher will return new value
     */
    public function testEmitFindWithHandler(): void
    {
        $newValue = 'new test';

        /** @var HandlerInterface|MockObject $handler */
        $handler = $this->getMockBuilder(Handler::class)->setMethods(['handle'])->getMock();
        $handler->expects($this->once())->method('handle')->willReturn($newValue);

        $this->searcher->setHandler($handler);
        $this->searcher->onFind(function ($param) use ($newValue) {
            $this->assertEquals($newValue, $param);
        });
        $this->searcher->emitFind($this->searchable);
    }

    /**
     * Test that search method find needle results
     *
     * @dataProvider searchProvider
     * @param string $text
     */
    public function testSearch(string $text): void
    {
        $pattern = '/'.$this->searchable.'/';
        $this->searcher->setPattern($pattern);
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
     * Test that search method NOT find results
     *
     * @dataProvider notSearchProvider
     * @param string $text
     */
    public function testNotSearch(string $text): void
    {
        $findCounter = 0;

        $pattern = '/'.$this->searchable.'/';
        $this->searcher->setPattern($pattern);
        $this->searcher->onFind(static function () use (&$findCounter) {
            $findCounter++;
        });
        $this->searcher->search($text);
        $this->assertEquals(0, $findCounter);
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
     * Test that search find all matches
     *
     * @dataProvider searchMatchesProvider
     * @param string $text
     * @param int $expectedCount
     */
    public function testSearchMatchesCount(string $text, int $expectedCount): void
    {
        $findCounter = 0;

        $pattern = '/'.$this->searchable.'/';
        $this->searcher->setPattern($pattern);
        $this->searcher->onFind(static function () use (&$findCounter) {
            $findCounter++;
        });
        $this->searcher->search($text);
        $this->assertEquals($expectedCount, $findCounter);
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
