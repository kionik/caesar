<?php

namespace Kionik\Caesar\Handlers\Xml;

use Kionik\Caesar\Handlers\Handler;

/**
 * Class SimpleXMLElementHandler
 *
 * @package Kionik\Caesar\Handlers\Xml
 */
class SimpleXMLElementHandler extends Handler
{
    /**
     * @param string $tag
     *
     * @return mixed
     */
    public function handle($tag)
    {
        $subject = new \SimpleXMLElement($tag);
        return parent::handle($subject);
    }
}