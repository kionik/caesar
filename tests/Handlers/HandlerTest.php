<?php

namespace Kionik\Tests\Caesar\Handlers;

use Kionik\Caesar\Handlers\Handler;
use Kionik\Caesar\Handlers\HandlerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    /**
     * @var HandlerInterface
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new class extends Handler {

            public function handle($subject)
            {
                $newSubject = 'handler value';
                return parent::handle($newSubject);
            }
        };
    }

    /**
     * Testing that handler return new value
     */
    public function testHandle(): void
    {
        $this->assertEquals('handler value', $this->handler->handle('value'));
    }

    /**
     * Testing that all handlers will be called
     */
    public function testHandleTwice(): void
    {
        $expected = 'second value';

        /** @var MockObject|HandlerInterface $handler2 */
        $handler2 = $this->createMock(Handler::class);
        $handler2->expects($this->once())->method('handle')->willReturn($expected);

        $this->handler->setNext($handler2);
        $result = $this->handler->handle('value');

        $this->assertEquals($expected, $result);
    }
}