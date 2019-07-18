<?php

namespace Kionik\Tests\Caesar\Parsers;

use Kionik\Caesar\Parsers\Parser;
use Kionik\Caesar\Searchers\Searcher;
use Kionik\Caesar\Searchers\SearcherInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * Test that parser works with searcher and call its method search
     */
    public function testParse(): void
    {
        /** @var MockObject|SearcherInterface $searcher */
        $searcher = $this->createMock(Searcher::class);
        $searcher->expects($this->atLeastOnce())->method('search');
        $parser = new Parser($searcher);
        $parser->parse('some test');
    }
}