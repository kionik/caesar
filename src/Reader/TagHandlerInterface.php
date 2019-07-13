<?php

namespace Kionik\ReactXml\Reader;

interface TagHandlerInterface
{
    /**
     * Method should handle $tag somehow and
     * back new result
     *
     * @param \SimpleXMLElement $tag
     *
     * @return mixed|null
     */
    public function handle(\SimpleXMLElement $tag);
}