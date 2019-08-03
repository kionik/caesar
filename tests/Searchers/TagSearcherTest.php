<?php

namespace Kionik\Tests\Caesar\Searchers;

use Kionik\Caesar\Searchers\TagSearcher;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class TagSearcherTest extends MockeryTestCase
{
    /**
     * Testing that tag will be correctly parsed
     * and returned in string
     *
     * @param string $subject
     * @param int $expectedFindCount
     * @param string $expectedTag
     *
     * @dataProvider tagFindProvider
     */
    public function testTagFind(string $subject, int $expectedFindCount, string $expectedTag = '<test>data</test>'): void
    {
        $tester = \Mockery::mock(\stdClass::class)->makePartial();
        $tester
            ->shouldNotReceive('testMethod')
            ->times($expectedFindCount)
            ->andReturnUsing(function (string $tag) use ($expectedTag) {
                $this->assertEquals($expectedTag, $tag);
            });

        $searcher = new TagSearcher('test');
        $searcher->onFind([$tester, 'testMethod']);
        $searcher->search($subject);
    }

    /**
     * @return array
     */
    public function tagFindProvider(): array
    {
        return [
            ['<test>data</test>', 1],
            ['<test>data</test><test>data</test>', 2],
            ['some test<test>data</test>some test', 1],
            ['some test<test>data</test><test>data</test>some test', 2],
            ['<test>data</test><test>data</test><test>data</test>', 3],
            ['<test someattr="value"    >  data  </test   >', 1, '<test someattr="value"    >  data  </test   >'],
            ['test<test someattr="value"    >  data  </test   > test', 1, '<test someattr="value"    >  data  </test   >'],
            ['<test <test>data</test> /test>', 1],
            ['<test>data</test>some test<test <test>data</test> /test><test>data</test>test some', 3],
            ['test>data</test><test<test>data</test>/test><testdata</test>test some', 1],
            ['<test><testdata</test</test>', 1, '<test><testdata</test</test>'],
            ['<test foo="bar"><test data</test</test>', 1, '<test foo="bar"><test data</test</test>'],
        ];
    }
}