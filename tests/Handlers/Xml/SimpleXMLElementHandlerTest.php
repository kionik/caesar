<?php

namespace Kionik\Tests\Caesar\Handlers\Xml;

use Kionik\Caesar\Handlers\Xml\SimpleXMLElementHandler;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SimpleXMLElementHandlerTest extends MockeryTestCase
{
    /**
     * Testing that after handling data method
     * will return instance of SimpleXMLElement
     */
    public function testSimpleXmlElement(): void
    {
        $handler = new SimpleXMLElementHandler();
        $result = $handler->handle('<test>data</test>');
        $this->assertEquals(new \SimpleXMLElement('<test>data</test>'), $result);
    }
}