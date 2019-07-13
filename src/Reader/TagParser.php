<?php

namespace Kionik\ReactXml\Reader;

use Evenement\EventEmitter;
use Kionik\ReactXml\Reader\Exceptions\XmlParseException;

class TagParser extends EventEmitter implements XmlParserInterface
{
    /**
     * Stores previous chunk, if it doesn't match
     *
     * @var string[]
     */
    protected $previousChunks = [];

    /**
     * Counter for cases when we have more than one mismatch
     *
     * @var int
     */
    protected $noMatchCount = 0;

    /**
     * Stores previous chunk end part, if it does match
     *
     * @var string
     */
    protected $previousChunkEnd = '';

    /**
     * Stores current chunk start part, if it does match
     *
     * @var string
     */
    protected $currentChunkStart = '';

    /**
     * Handler for matches tags
     *
     * @var TagHandlerInterface
     */
    protected $handler;

    /**
     * Pattern for matching chunks
     *
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $searchTag;

    /**
     * XmlRowParser constructor.
     *
     * @param string $searchTag
     */
    public function __construct(string $searchTag)
    {
        $this->searchTag = $searchTag;
        $this->pattern = "/<\s*?{$searchTag}\b[^>]*>(.*?)<\/{$searchTag}\b[^>]*>/s";
    }

    /**
     * @param TagHandlerInterface $handler
     */
    public function setHandler(TagHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param string $chunk
     *
     * @throws XmlParseException
     */
    public function parse(string $chunk): void
    {
        // If we found no matches in previous chunks, then add previous chunks to current chunk.
        // Else if we found matches, then add previous chunk start part to current chunk
        if (count($this->previousChunks) > 0)
            $fullChunk = implode('', $this->previousChunks) . $chunk;
        else
            $fullChunk = $this->previousChunkEnd . $chunk;

        // If we found matches in current tag, then send each matches tag.
        // Handle previous chunk end part and current chunk start part.
        // Set matches flag to true. Unset previous chunks value.
        if ($matches = $this->getMatches($fullChunk)) {

            foreach ($matches as $match)
                $this->sendTag($match[0]);

            $this->handlePreviousCurrentChunk($fullChunk);

            // Unset previous chunks, because we have already filled previousChunkEnd
            $this->previousChunks = [];
        } else {

            // If we not found matches in 2 previous chunks, then we need unset first previous chunk because of memory limit
            if (count($this->previousChunks) > 1
                && !preg_match("/<\s*?{$this->searchTag}\b[^>]*>/s", implode('', $this->previousChunks))
            ) {
                array_shift($this->previousChunks);
            }

            // If no matches in current chunk, then remember current chunk
            $this->previousChunks[] = $chunk;

                // Unset previous chunk end, because we already have fill previous chunk
            $this->previousChunkEnd = '';
        }
    }

    /**
     * Handle tag by middleware TagHandlerInterface
     * Emit found tag event
     *
     * @param string $xmlTag
     *
     * @throws XmlParseException
     */
    public function sendTag(string $xmlTag): void
    {
        try {
            $xmlElement = new \SimpleXMLElement($xmlTag, LIBXML_NOERROR);
        } catch (\Throwable $e) {
            throw new XmlParseException("Can't read {$xmlTag}. Tag isn't valid xml object");
        }

        // If isset handler, then handle $xmlElement and return it result
        // Else return SimpleXmlElement
        $xmlTag = $this->handler ? $this->handler->handle($xmlElement) : $xmlElement;

        if ($xmlTag !== null)
            $this->emit('find', [$xmlTag]);
    }

    /**
     * Get matches in chunk by pattern
     *
     * @param string $chunk
     *
     * @return array|null
     */
    private function getMatches(string $chunk): ?array
    {
        if (preg_match_all($this->pattern, $chunk, $matches, PREG_SET_ORDER))
            return $matches;

        return null;
    }

    /**
     * Handle part between last found tag in previous chunk and first found tag in current chunk
     *
     * @param string $chunk
     *
     * @throws XmlParseException
     */
    private function handlePreviousCurrentChunk(string $chunk): void
    {
        $replacement = uniqid();

        // Change all find tags to $replacement
        $replacedContent = preg_replace($this->pattern, $replacement, $chunk);

        // If previous chunk matches, then get current chunk start part.
        // Try to match previous chunk end and current chunk start.
        // If matches, then send matches tags
        if (count($this->previousChunks) === 0) {

            $this->currentChunkStart = $this->getChunkStart($replacedContent, $replacement);

            if ($matches = $this->getMatches($this->previousChunkEnd . $this->currentChunkStart))
                foreach ($matches as $match)
                    $this->sendTag($match[0]);
        }

        // Remember current chunk end part for the next chunk
        $this->previousChunkEnd = $this->getChunkEnd($replacedContent, $replacement);
    }

    /**
     * Get chunk part after last founded tag
     *
     * @param string $replacedContent
     * @param string $search
     *
     * @return string
     */
    private function getChunkEnd(string $replacedContent, string $search): string
    {
        return str_replace($search, '', substr($replacedContent, strrpos($replacedContent, $search)));
    }

    /**
     * Get chunk part before first founded tag
     *
     * @param string $replacedContent
     * @param string $search
     *
     * @return string
     */
    private function getChunkStart(string $replacedContent, string $search): string
    {
        return str_replace($search, '', substr($replacedContent, 0, strpos($replacedContent, $search)));
    }
}