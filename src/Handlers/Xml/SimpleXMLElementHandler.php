<?php

namespace Kionik\Caesar\Handlers\Xml;

use Kionik\Caesar\Handlers\Handler;

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