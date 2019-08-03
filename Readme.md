# Caesar

Caesar is a library based on ReactPHP technology that provides to read strings and files in an asynchronous way and find matches in them by patterns.

# Basic usage

```php
$loop = \React\EventLoop\Factory::create();
$reader = new \Kionik\Caesar\Reader($loop);
$reader->onFind('/foo/', function (string $foo) {
    echo $foo; // return 'foo'
});
$reader->onEnd(function() {
    echo PHP_EOL . 'finish reading';
});
$reader->read('foo bar');

// some code...

// This can be replaced by $loop->run();
$reader->run();
```
`Reader` working only with a strings, parse it and return result of parsing if found something.

# FileReader

`FileReader` working only with a resources. Inside of read method use `React\Stream\ReadableResourceStream` of ReactPHP library that provides to read file in asynchronous stream way. 

```php
$fileName = 'foo.txt';
file_put_contents($fileName, 'foo bar');
$loop = \React\EventLoop\Factory::create();
$reader = new \Kionik\Caesar\FileReader($loop);
$reader->onFind('/foo/', function (string $foo) {
    echo $foo; // return 'foo'
});
$reader->read(fopen($fileName, 'rb'));

// some code...

$reader->run();
unlink($fileName);
```
You can change size of one chunk by using second parameter `$chunkSize` of `read` method
```php
$chunkSize = 1;
$reader->read(fopen('foo.txt', 'rb'), $chunkSize);
```
If you change chunk size then don't forget to change `Parser::$storeChunksCount` by using 
```php
$reader->setStoreChunksCount(10);
```
This parameter is responsible for the maximum number of chunks that will stored in `FileReader` memory, if `FileReader` wouldn't find something in previous chunks.

# XmlStringReader
```php
$loop = \React\EventLoop\Factory::create();
$reader = new \Kionik\Caesar\XmlStringReader($loop);
$reader->onFind('foo', function (string $foo) {
    echo $foo; // return '<foo>bar</foo>'
});
$reader->read('<foo>bar</foo><bar>foo</bar>');

// some code...

$reader->run();
```

# XmlFileReader
```php
$fileName = 'foo.xml';
$xmlWriter = new \XMLWriter();
$xmlWriter->openMemory();
$xmlWriter->startDocument('1.0', 'UTF-8');
$xmlWriter->writeElement('foo', 'bar');
$xmlWriter->writeElement('bar', 'foo');
file_put_contents($fileName, $xmlWriter->flush(), FILE_APPEND);

$loop = \React\EventLoop\Factory::create();
$reader = new \Kionik\Caesar\XmlFileReader($loop);
$reader->onFind('foo', function (string $foo) {
    echo $foo; // return '<foo>bar</foo>'
});
$reader->read(fopen($fileName, 'rb'));

// some code...

$reader->run();
unlink($fileName);
```

# Handlers

All readers provides to use handlers for handling data before it returns to `$listener`.
```php
$loop = \React\EventLoop\Factory::create();
$reader = new \Kionik\Caesar\XmlStringReader($loop);

$reader
    ->onFind('tag', function (SimpleXMLElement $tag) {
        echo $tag->attributes()->getName(); // return 'foo'
    })
    ->handler(new \Kionik\Caesar\Handlers\Xml\SimpleXMLElementHandler());

$reader->read('<tag foo="bar"></tag>');

// some code...

$reader->run();
```

# Licensing

All of the code in this library is licensed under the MIT license as included in the LICENSE file.