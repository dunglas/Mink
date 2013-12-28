<?php

namespace Behat\Mink\Tests\Exception;

use Behat\Mink\Exception\ExpectationException;

class ExpectationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyMessageAndPreviousException()
    {
        $exception = new ExpectationException('', $this->getMock('Behat\Mink\Driver\DriverInterface'), new \Exception('Something failed'));

        $this->assertEquals('Something failed', $exception->getMessage());
    }

    public function testExceptionToString()
    {
        $driver = $this->getMock('Behat\Mink\Driver\DriverInterface');

        $driver->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $driver->expects($this->any())
            ->method('getCurrentUrl')
            ->will($this->returnValue('http://localhost/test'));

        $html = "<html><head><title>Hello</title></head>\n<body>\n<h1>Hello world</h1>\n<p>Test</p>\n</body></html>";
        $driver->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($html));

        $expected = <<<'TXT'
Expectation failure

+--[ HTTP/1.1 200 | http://localhost/test | %s ]
|
|  <body>
|  <h1>Hello world</h1>
|  <p>Test</p>
|  </body>
|
TXT;

        $expected = sprintf($expected.'  ', get_class($driver));

        $exception = new ExpectationException('Expectation failure', $driver);

        $this->assertEquals($expected, $exception->__toString());
    }

    public function testExceptionWhileRenderingString()
    {
        $driver = $this->getMock('Behat\Mink\Driver\DriverInterface');
        $driver->expects($this->any())
            ->method('getContent')
            ->will($this->throwException(new \Exception('Broken page')));

        $exception = new ExpectationException('Expectation failure', $driver);

        $this->assertEquals('Expectation failure', $exception->__toString());
    }
}
