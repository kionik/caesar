<?php

namespace Kionik\Caesar\Parsers;

use Evenement\EventEmitter;
use Kionik\Caesar\Searchers\SearcherInterface;

/**
 * Class Parser
 *
 * @package Kionik\Caesar\Parsers
 */
class Parser extends EventEmitter implements ParserInterface
{
    /**
     * @var SearcherInterface
     */
    protected $searcher;

    /**
     * Stores previous chunk, if it does not match
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
     * Parser constructor.
     *
     * @param SearcherInterface $searcher
     */
    public function __construct(SearcherInterface $searcher)
    {
        $this->setSearcher($searcher);
    }

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
     * @return SearcherInterface
     */
    public function getSearcher(): SearcherInterface
    {
        return $this->searcher;
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

            $this->previousChunkEnd = $this->getChunkEnd($fullChunk);

        } else {

            // If no matches in current chunk, then remember current chunk
            $this->previousChunks[] = $chunk;

            // If we not found matches in $maxChunksCount previous chunks,
            // then we need unset first previous chunk because of memory limit
            $this->shiftFirstChunk();

            // Unset previous chunk end, because we already have filled previous chunk
            $this->previousChunkEnd = '';
        }
    }

    /**
     * Get chunk part after last founded searchable
     *
     * @param string $chunk
     *
     * @return string
     */
    public function getChunkEnd(string $chunk): string
    {
        $replacement = uniqid('file-chunk-', false);
        $replacedContent = preg_replace($this->searcher->getPattern(), $replacement, $chunk);
        return str_replace($replacement, '', substr($replacedContent, strrpos($replacedContent, $replacement)));
    }

    /**
     * Function delete first chunk if count of previous chunks more then maximum chunks count
     */
    protected function shiftFirstChunk(): void
    {
        if (count($this->previousChunks) > $this->maxNumOfStoredPreviousChunks) {
            array_shift($this->previousChunks);
        }
    }
}