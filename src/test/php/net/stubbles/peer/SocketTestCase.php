<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer;
/**
 * Test for net\stubbles\peer\Socket.
 *
 * @group  peer
 */
class SocketTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function createWithEmptyHostThrowsIllegalArgumentException()
    {
        new Socket('');
    }

    /**
     * @test
     */
    public function containsGivenHost()
    {
        $socket = new Socket('example.com');
        $this->assertEquals('example.com', $socket->getHost());
    }

    /**
     * @test
     */
    public function portDefaultsTo80()
    {
        $socket = new Socket('example.com');
        $this->assertEquals(80, $socket->getPort());
    }

    /**
     * @test
     */
    public function hasNoPrefixByDefault()
    {
        $socket = new Socket('example.com');
        $this->assertNull($socket->getPrefix());
    }

    /**
     * @test
     */
    public function timeoutDefaultsTo5Seconds()
    {
        $socket = new Socket('example.com');
        $this->assertEquals(5, $socket->getTimeout());
    }

    /**
     * @test
     */
    public function timeoutCanBeChanged()
    {
        $socket = new Socket('example.com');
        $this->assertEquals(60, $socket->setTimeout(60)->getTimeout());
    }

    /**
     * @test
     */
    public function isNotConnectedAfterCreation()
    {
        $socket = new Socket('example.com');
        $this->assertFalse($socket->isConnected());
    }

    /**
     * @test
     */
    public function isAtEndOfSocketAfterCreation()
    {
        $socket = new Socket('example.com');
        $this->assertTrue($socket->eof());
    }

    /**
     * @test
     */
    public function hasGivenPort()
    {
        $socket = new Socket('example.com', 443, 'ssl://', 30);
        $this->assertEquals(443, $socket->getPort());
    }

    /**
     * @test
     */
    public function hasGivenPrefix()
    {
        $socket = new Socket('example.com', 443, 'ssl://', 30);
        $this->assertEquals('ssl://', $socket->getPrefix());
    }

    /**
     * @test
     */
    public function hasGivenTimeout()
    {
        $socket = new Socket('example.com', 443, 'ssl://', 30);
        $this->assertEquals(30, $socket->getTimeout());
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function readOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new Socket('example.com');
        $socket->read();
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function readLineOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new Socket('example.com');
        $socket->readLine();
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function readBinaryOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new Socket('example.com');
        $socket->readBinary();
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function writeOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new Socket('example.com');
         $socket->write('data');
    }

    /**
     * @test
     */
    public function disconnectReturnsInstance()
    {
        $socket = new Socket('example.com');
        $this->assertSame($socket, $socket->disconnect());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canBeUsedAsInputStream()
    {
        $this->assertInstanceOf('net\\stubbles\\streams\\InputStream',
                                $this->getMock('net\\stubbles\\peer\\Socket',
                                               array('connect'),
                                               array('localhost')
                                       )
                                     ->getInputStream()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canBeUsedAsOutputStream()
    {
        $this->assertInstanceOf('net\\stubbles\\streams\\OutputStream',
                                $this->getMock('net\\stubbles\\peer\\Socket',
                                               array('connect'),
                                               array('localhost')
                                       )
                                     ->getOutputStream()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\ConnectionException
     */
    public function failureOnConnectThrowsConnectionException()
    {
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->with($this->equalTo('example.com'), $this->equalTo(80))
                  ->will($this->returnValue(false));
        $socket = new Socket('example.com');
        $socket->connect();
    }

    /**
     * @test
     */
    public function setsTimeoutOnConnect()
    {
        $mockfp = 'mocked connection pointer';
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->with($this->equalTo('example.com'), $this->equalTo(303))
                  ->will($this->returnValue($mockfp));
        $socket_set_timeout = $this->getFunctionMock('socket_set_timeout', __NAMESPACE__);
        $socket_set_timeout->expects($this->once())
                           ->with($this->equalTo($mockfp), $this->equalTo(5));
        $socket = new Socket('example.com', 303);
        $socket->connect();
    }

    /**
     * @test
     */
    public function connectDoesNotConnectAgainIfAlreadyConnected()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $socket = new Socket('example.com');
        $socket->connect();
        $socket->connect();
        fclose($mockfp);
    }

    /**
     * @test
     */
    public function disconnectClosesConnection()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $socket = new Socket('example.com');
        $socket->connect();
        $fclose = $this->getFunctionMock('fclose', __NAMESPACE__);
        $fclose->expects($this->atLeastOnce())
               ->with($mockfp);
        $socket->disconnect();
        fclose($mockfp);
    }

    /**
     * @test
     */
    public function setTimeoutChangesTimeoutOfAlreadyOpenedConnection()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $socket_set_timeout = $this->getFunctionMock('socket_set_timeout', __NAMESPACE__);
        $socket_set_timeout->expects($this->at(0))
                           ->with($this->equalTo($mockfp), $this->equalTo(5));
        $socket_set_timeout->expects($this->at(1))
                           ->with($this->equalTo($mockfp), $this->equalTo(2));
        $socket = new Socket('example.com');
        $socket->connect();
        $socket->setTimeout(2);
        fclose($mockfp);
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\ConnectionException
     */
    public function failureWhileReadingThrowsConnectionException()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $fgets = $this->getFunctionMock('fgets', __NAMESPACE__);
        $fgets->expects(($this->once()))
              ->will($this->returnValue(false));
        $feof = $this->getFunctionMock('feof', __NAMESPACE__);
        $feof->expects(($this->once()))
             ->will($this->returnValue(false));
        $socket = new Socket('example.com');
        $socket->connect();
        $socket->read();
        fclose($mockfp);
    }

    /**
     * @test
     */
    public function readEndOfSocketReturnsNull()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $fgets = $this->getFunctionMock('fgets', __NAMESPACE__);
        $fgets->expects(($this->once()))
              ->will($this->returnValue(false));
        $feof = $this->getFunctionMock('feof', __NAMESPACE__);
        $feof->expects(($this->once()))
             ->will($this->returnValue(true));
        $socket = new Socket('example.com');
        $socket->connect();
        $this->assertNull($socket->read());
        fclose($mockfp);
    }

    /**
     * @test
     */
    public function returnsDataReadFromSocket()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $fgets = $this->getFunctionMock('fgets', __NAMESPACE__);
        $fgets->expects(($this->once()))
              ->will($this->returnValue('some data'));
        $socket = new Socket('example.com');
        $socket->connect();
        $this->assertEquals('some data', $socket->read());
        fclose($mockfp);
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\ConnectionException
     */
    public function failureWhileReadingBinaryThrowsConnectionException()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $fread = $this->getFunctionMock('fread', __NAMESPACE__);
        $fread->expects(($this->once()))
              ->will($this->returnValue(false));
        $socket = new Socket('example.com');
        $socket->connect();
        $socket->readBinary();
        fclose($mockfp);
    }

    /**
     * @test
     */
    public function returnsBinaryDataReadFromSocket()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $fread = $this->getFunctionMock('fread', __NAMESPACE__);
        $fread->expects(($this->once()))
              ->will($this->returnValue('some data'));
        $socket = new Socket('example.com');
        $socket->connect();
        $this->assertEquals('some data', $socket->readBinary());
        fclose($mockfp);
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\ConnectionException
     */
    public function failureWhileWritingThrowsConnectionException()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $fputs = $this->getFunctionMock('fputs', __NAMESPACE__);
        $fputs->expects(($this->once()))
              ->with($this->equalTo($mockfp), $this->equalTo('some data'), $this->equalTo(9))
              ->will($this->returnValue(false));
        $socket = new Socket('example.com');
        $socket->connect();
        $socket->write('some data');
        fclose($mockfp);
    }

    /**
     * @test
     */
    public function writeDataToSocket()
    {
        $mockfp = fopen(__FILE__, 'r');
        $fsockopen = $this->getFunctionMock('fsockopen', __NAMESPACE__);
        $fsockopen->expects($this->once())
                  ->will($this->returnValue($mockfp));
        $fputs = $this->getFunctionMock('fputs', __NAMESPACE__);
        $fputs->expects(($this->once()))
              ->with($this->equalTo($mockfp), $this->equalTo('some data'), $this->equalTo(9))
              ->will($this->returnValue(9));
        $socket = new Socket('example.com');
        $socket->connect();
        $this->assertEquals(9, $socket->write('some data'));
        fclose($mockfp);
    }
}
?>