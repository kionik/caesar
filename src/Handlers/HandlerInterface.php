<?php

namespace Kionik\Caesar\Handlers;

/**
 * Middleware pattern
 *
 * Interface HandlerInterface
 *
 * @package Kionik\Caesar\Handlers
 */
interface HandlerInterface
{
    /**
     * @param HandlerInterface $handler
     *
     * @return HandlerInterface
     */
    public function setNext(HandlerInterface $handler): HandlerInterface;

    /**
     * @param $subject
     *
     * @return mixed
     */
    public function handle($subject);
}