<?php

namespace Kionik\Tests\Caesar\Handlers;

use Kionik\Caesar\Handlers\Handler;
use Kionik\Caesar\Handlers\HandlerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;

class HandlerTest extends MockeryTestCase
{
    /**
     * @var HandlerInterface
     */
    protected $handler;

    public function setUp(): void
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

        /** @var Mock|HandlerInterface $handler2 */
        $handler2 = \Mockery::mock(Handler::class);
        $handler2->shouldReceive('handle')->once()->andReturn($expected);

        $this->handler->setNext($handler2);
        $result = $this->handler->handle('value');

        $this->assertEquals($expected, $result);
    }
}