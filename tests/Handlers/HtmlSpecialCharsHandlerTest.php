<?php

namespace Kionik\Tests\Caesar\Handlers;

use Kionik\Caesar\Handlers\HtmlSpecialCharsHandler;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class HtmlSpecialCharsHandlerTest extends MockeryTestCase
{
    /**
     * Testing that after handle data method
     * will return same result as htmlspecialchars function
     */
    public function testHtmlSpecialChars(): void
    {
        $handler = new HtmlSpecialCharsHandler();
        $result = $handler->handle('<test>&data&</test>');
        $this->assertEquals(htmlspecialchars('<test>&data&</test>'), $result);
    }
}