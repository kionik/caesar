<?php

namespace Kionik\Tests\Caesar;

use Kionik\Caesar\Parsers\Parser;
use Kionik\Caesar\Searchers\TagSearcher;
use Kionik\Caesar\XmlFileReader;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use React\EventLoop\Factory;

class XmlFileReaderTest extends MockeryTestCase
{
    /**
     * Testing that created searcher object will be
     * instance of TagSearcher
     */
    public function testIsTagSearcherClass(): void
    {
        $reader = new XmlFileReader(Factory::create());
        $reader->onFind('/test/', static function () {});
        /** @var Parser $parser */
        $parser = $reader->parsers()->last();
        $this->assertEquals(TagSearcher::class, get_class($parser->getSearcher()));
    }

    /**
     * Testing that file correctly read by FileReader
     */
    public function testRead(): void
    {
        $fileName = 'example.xml';
        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('shop');
        for ($i=0; $i<=99; ++$i) {
            $productId = uniqid('product-', false);
            $xmlWriter->startElement('product');
            $xmlWriter->writeElement('id', $productId);
            $xmlWriter->writeElement('name', 'Some product name. ID:' . $productId);
            $xmlWriter->endElement();
            // Flush XML in memory to file every 1000 iterations
            if (0 == $i%1000) {
                file_put_contents($fileName, $xmlWriter->flush(), FILE_APPEND);
            }
        }
        $xmlWriter->endElement();
        // Final flush to make sure we haven't missed anything
        file_put_contents($fileName, $xmlWriter->flush(), FILE_APPEND);

        $tester = \Mockery::mock(\stdClass::class)->makePartial();
        $tester->shouldReceive('findProduct')->times(100);
        $tester->shouldReceive('findName')->times(100);
        $tester->shouldNotReceive('findTest');
        $reader = new XmlFileReader(Factory::create());
        $reader->onFind('product', [$tester, 'findProduct']);
        $reader->onFind('name', [$tester, 'findName']);
        $reader->onFind('test', [$tester, 'findTest']);
        $reader->read(fopen($fileName, 'rb'));
        $reader->run();

        unlink($fileName);
    }
}