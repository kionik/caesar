<?php

namespace Kionik\Tests\Caesar\Parsers;

use Kionik\Caesar\Parsers\ChunkParser;
use Kionik\Caesar\Searchers\Searcher;
use Kionik\Caesar\Searchers\SearcherInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;

class ChunkParserTest extends MockeryTestCase
{
    /** @var Mock|SearcherInterface */
    protected $searcher;

    /** @var ChunkParser */
    protected $parser;

    public function setUp(): void
    {
        $this->searcher = \Mockery::mock(Searcher::class)->makePartial();
        $this->searcher->setPattern('/test/');
        $this->parser = new ChunkParser($this->searcher);
    }

    /**
     * Testing that parser will find all matches in simple chunks
     *
     * @dataProvider
     */
    public function testSimpleParse(): void
    {
        $chunks = ['first test', 'second test', 'third test'];

        $this->searcher->shouldReceive('emitFind')->times(3)->andReturnUsing(function () {
            $isFoundProperty = new \ReflectionProperty($this->parser, 'isFound');
            $isFoundProperty->setAccessible(true);
            $isFoundProperty->setValue($this->parser, true);
        });

        foreach ($chunks as $chunk) {
            $this->parser->parse($chunk);
        }
    }

    /**
     * Testing that parser will find matches in cases when searchable is between two chunks
     *
     * @param array $chunks
     * @param int $expectedFound
     * @param int $storedChunks
     * @dataProvider betweenChunksProvider
     */
    public function testParseBetweenChunks(array $chunks, int $expectedFound, int $storedChunks): void
    {
        $this->searcher->shouldReceive('emitFind')->times($expectedFound)->andReturnUsing(function () {
            $isFoundProperty = new \ReflectionProperty($this->parser, 'isFound');
            $isFoundProperty->setAccessible(true);
            $isFoundProperty->setValue($this->parser, true);
        });

        $this->parser->setMaxNumOfStoredPreviousChunks($storedChunks);

        foreach ($chunks as $chunk) {
            $this->parser->parse($chunk);
        }
    }

    public function betweenChunksProvider(): array
    {
        return [
            [['first te', 'st second tes', 't third test'], 3, 1],
            [['tes', 'test'], 1, 1],
            [['t', 'e', 's', 'test', 'est', 'test'], 3, 3],
            [['t', '', 'e', '', 's', '', 't', 'test', 'est'], 2, 7],
        ];
    }

    /**
     * Testing that parser stores necessary count of previous chunks
     *
     * @param array $chunks
     *
     * @dataProvider countPreviousChunksProvider
     */
    public function testCountPreviousChunks(array $chunks): void
    {
        $this->parser->setMaxNumOfStoredPreviousChunks(5);

        foreach ($chunks as $chunk) {
            $this->parser->parse($chunk);
        }

        $this->assertCount(count($chunks), $this->parser->getPreviousChunks());
        $this->assertEquals(implode('', $chunks), implode('', $this->parser->getPreviousChunks()));
    }

    public function countPreviousChunksProvider(): array
    {
        return [
            [['not match', 'not match', 'not match', 'not match', 'not match']],
            [['not match', 'not match', 'not match', 'not match']],
            [['not match', 'not match', 'not match']],
            [['not match', 'not match']],
            [['not match']],
        ];
    }

    /**
     * Testing that parser doesn't stored more than maximum number of previous chunks
     *
     * @param array $chunks
     * @param int $storedChunks
     *
     * @dataProvider countPreviousChunksNegativeProvider
     */
    public function testCountPreviousChunksNegative(array $chunks, int $storedChunks): void
    {
        $this->parser->setMaxNumOfStoredPreviousChunks($storedChunks);

        foreach ($chunks as $chunk) {
            $this->parser->parse($chunk);
        }

        $this->assertCount($storedChunks, $this->parser->getPreviousChunks());
        $this->assertNotEquals(implode('', $chunks), implode('', $this->parser->getPreviousChunks()));
    }

    public function countPreviousChunksNegativeProvider(): array
    {
        return [
            [['n', 'o', 't', 'm', 'a', 't', 'c', 'h'], 4],
            [['n', 'o', 't', 'm', 'a', 't', 'c', 'h'], 3],
            [['n', 'o', 't', 'm', 'a', 't', 'c', 'h'], 2],
            [['n', 'o', 't', 'm', 'a', 't', 'c', 'h'], 1],
            [['n', 'o', 't', 'm', 'a', 't', 'c', 'h'], 0],
        ];
    }

    /**
     * Testing that parser reset previous chunks to [] if searcher found matches
     *
     * @param array $chunks
     * @param int $expectedCount
     *
     * @dataProvider resetPreviousChunksProvider
     */
    public function testResetPreviousChunks(array $chunks, int $expectedCount): void
    {
        $this->parser->setMaxNumOfStoredPreviousChunks(3);

        foreach ($chunks as $chunk) {
            $this->parser->parse($chunk);
        }

        $this->assertCount($expectedCount, $this->parser->getPreviousChunks());
    }

    public function resetPreviousChunksProvider(): array
    {
        return [
            [['not match', 'match test'], 0],
            [['not match', 'match test', 'not match', 'match test'], 0],
            [['not match', 'match test', 'not match'], 1],
            [['not match', 'match test', 'not match', 'not match'], 2],
            [['not match', 'match test', 'not match', 'not match', 'not match'], 3],
        ];
    }
}