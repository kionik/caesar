<?php

namespace Kionik\Caesar\Handlers;

/**
 * Class StripTagsHandler
 *
 * @package Kionik\Caesar\Handlers
 */
class StripTagsHandler extends Handler
{
    /**
     * @param $subject
     *
     * @return mixed|void
     */
    public function handle($subject)
    {
        return parent::handle(strip_tags($subject));
    }
}