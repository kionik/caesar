<?php

namespace Kionik\Caesar\Handlers;

/**
 * Class Handler
 *
 * @package Kionik\Caesar\Handlers
 */
abstract class Handler implements HandlerInterface
{
    /**
     * @var Handler
     */
    private $nextHandler;

    /**
     * @param HandlerInterface $handler
     *
     * @return Handler
     */
    public function setNext(HandlerInterface $handler): HandlerInterface
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    /**
     * @param $subject
     *
     * @return mixed
     */
    public function handle($subject)
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($subject);
        }

        return $subject;
    }
}