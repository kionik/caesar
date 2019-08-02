<?php

namespace Kionik\Tests\Caesar\Handlers;

use Kionik\Caesar\Handlers\StripTagsHandler;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class StripTagsHandlerTest extends MockeryTestCase
{
    /**
     * Testing that after handle data method
     * will return same result as striptags function
     */
    public function testStripTags(): void
    {
        $handler = new StripTagsHandler();
        $result = $handler->handle('<test>&data&</test>');
        $this->assertEquals(strip_tags('<test>&data&</test>'), $result);
    }
}