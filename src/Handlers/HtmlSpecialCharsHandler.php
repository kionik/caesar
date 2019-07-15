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
        $subject = htmlspecialchars($subject);
        return parent::handle($subject);
    }
}