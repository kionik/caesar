<?php

namespace Kionik\Caesar\Handlers;

/**
 * Class HtmlSpecialCharsHandler
 *
 * @package Kionik\Caesar\Handlers
 */
class HtmlSpecialCharsHandler extends Handler
{
    /**
     * @param $subject
     *
     * @return mixed
     */
    public function handle($subject)
    {
        return parent::handle(htmlspecialchars($subject));
    }
}