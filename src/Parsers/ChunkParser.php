<?php

namespace Kionik\Caesar\Parsers;

use Kionik\Caesar\Searchers\SearcherInterface;

/**
 * Class FileParser
 *
 * @package Kionik\Caesar\Parsers
 */
class ChunkParser extends Parser
{
    /**
     * Stores previous chunk, if it doesn't match
     *
     * @var string[]
     */
    protected $previousChunks = [];

    /**
     * Stores previous chunk end part, if it does match
     *
     * @var string
     */
    protected $previousChunkEnd = '';

    /**
     * Maximum chunks count, that will store in $previousChunks
     *
     * @var int
     */
    protected $maxNumOfStoredPreviousChunks = 3;

    /**
     * @var bool
     */
    protected $isFound = false;

    /**
     * @param SearcherInterface $searcher
     */
    public function setSearcher(SearcherInterface $searcher): void
    {
        $this->searcher = $searcher;
        $this->searcher->onFind(function () {
            $this->isFound = true;
        });
    }

    /**
     * @return array
     */
    public function getPreviousChunks(): array
    {
        return $this->previousChunks;
    }

    /**
     * Set maximum chunks count, that will check on search pattern
     *
     * @param int $maxNumOfStoredPreviousChunks
     */
    public function setMaxNumOfStoredPreviousChunks(int $maxNumOfStoredPreviousChunks): void
    {
        $this->maxNumOfStoredPreviousChunks = $maxNumOfStoredPreviousChunks;
    }

    /**
     * Get maximum chunks count, that will check on search pattern
     *
     * @return int
     */
    public function getMaxNumOfStoredPreviousChunks(): int
    {
        return $this->maxNumOfStoredPreviousChunks;
    }

    /**
     * Method get file chunk from stream, add previous chunks if didn't found
     * something before. Search matches in previous chunks + current chunk.
     *
     * @param string $chunk
     */
    public function parse(string $chunk): void
    {
        $this->isFound = false;

        // If we found no matches in previous chunks, then add previous chunks to current chunk.
        // Else if we found matches, then add previous chunk start part to current chunk
        if (count($this->previousChunks) > 0) {
            $fullChunk = implode('', $this->previousChunks) . $chunk;
        }
        else {
            $fullChunk = $this->previousChunkEnd . $chunk;
        }

        $this->searcher->search($fullChunk);

        // If we found matches in current tag, then send each matches tag.
        // Handle previous chunk end part and current chunk start part.
        // Set matches flag to true. Unset previous chunks value.

        if ($this->isFound) {

            // Unset previous chunks, because we have already filled previousChunkEnd
            $this->previousChunks = [];

            $this->handleSpaceBetweenChunks($fullChunk);
        } else {

            // If no matches in current chunk, then remember current chunk
            $this->previousChunks[] = $chunk;

            // If we not found matches in $maxChunksCount previous chunks,
            // then we need unset first previous chunk because of memory limit
            $this->shiftFirstChunk();

                // Unset previous chunk end, because we already have fill previous chunk
            $this->previousChunkEnd = '';
        }
    }

    /**
     * Function delete first chunk in previous chunk queue by condition.
     */
    protected function shiftFirstChunk(): void
    {
        if (count($this->previousChunks) > $this->maxNumOfStoredPreviousChunks) {
            array_shift($this->previousChunks);
        }
    }

    /**
     * Handle part between last found tag in previous chunk and first found tag in current chunk
     *
     * @param string $chunk
     */
    protected function handleSpaceBetweenChunks(string $chunk): void
    {
        $replacement = uniqid('file-chunk-', false);

        // Change all find elements to $replacement
        $replacedContent = preg_replace($this->searcher->getPattern(), $replacement, $chunk);

        // If previous chunk matches, then get current chunk start part.
        // Try to match part between last found tag in previous chunk and first found tag in current chunk.
        $currentChunkStart = $this->getChunkStart($replacedContent, $replacement);
        $this->searcher->search($this->previousChunkEnd . $currentChunkStart);

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
    protected function getChunkEnd(string $replacedContent, string $search): string
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
    protected function getChunkStart(string $replacedContent, string $search): string
    {
        return str_replace($search, '', substr($replacedContent, 0, strpos($replacedContent, $search)));
    }
}