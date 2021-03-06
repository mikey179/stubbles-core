<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer\http;
use stubbles\streams\memory\MemoryInputStream;
/**
 * Test for stubbles\peer\http\HttpResponse.
 *
 * @group  peer
 * @group  peer_http
 */
class HttpResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * creates instance to test
     *
     * @param   string  $response  content of response
     * @return  HttpResponse
     */
    private function createResponse($response)
    {
        return HttpResponse::create(new MemoryInputStream($response));
    }

    /**
     * @test
     */
    public function chunkedResponseCanBeRead()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['HTTP/1.1 200 OK',
                         'Host: ' . __METHOD__,
                         'Transfer-Encoding: chunked',
                         '',
                         dechex(3) . " ext\r\n",
                         "foo\r\n",
                         dechex(3) . "\r\n",
                         "bar\r\n",
                         dechex(0) . "\r\n"
                        ]
                )
        );
        $this->assertEquals('foobar', $httpResponse->body());
        $headerList = $httpResponse->headers();
        $this->assertInstanceOf('stubbles\peer\HeaderList', $headerList);
        $this->assertEquals(__METHOD__, $headerList->get('Host'));
        $this->assertEquals(6, $headerList->get('Content-Length'));

    }

    /**
     * @test
     */
    public function nonChunkedResponseWithoutContentLengthHeaderCanBeRead()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['HTTP/1.1 200 OK',
                         'Host: ' . __METHOD__,
                         '',
                         'foobar'
                        ]
                )
        );
        $headerList = $httpResponse->headers();
        $this->assertInstanceOf('stubbles\peer\HeaderList', $headerList);
        $this->assertEquals(__METHOD__, $headerList->get('Host'));
        $this->assertEquals('foobar', $httpResponse->body());
    }

    /**
     * @test
     */
    public function nonChunkedResponseWithContentLengthHeaderCanBeRead()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['HTTP/1.1 200 OK',
                         'Host: ' . __METHOD__,
                         'Content-Length: 6',
                         '',
                         'foobar'
                        ]
                )
        );
        $headerList = $httpResponse->headers();
        $this->assertInstanceOf('stubbles\peer\HeaderList', $headerList);
        $this->assertEquals(__METHOD__, $headerList->get('Host'));
        $this->assertEquals(6, $headerList->get('Content-Length'));
        $this->assertEquals('foobar', $httpResponse->body());
    }

    /**
     * @test
     */
    public function canReadResponseTwice()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['HTTP/1.1 200 OK',
                         'Host: ' . __METHOD__,
                         'Content-Length: 6',
                         '',
                         'foobar'
                        ]
                )
                        );
        $this->assertEquals('foobar', $httpResponse->body());
        $this->assertEquals('foobar', $httpResponse->body());
    }

    /**
     * @test
     */
    public function continuesOnStatusCode100()
    {
        $httpResponse = $this->createResponse(
                Http::line('HTTP/1.0 100 Continue')
                . Http::line('Host: ' . __METHOD__)
                . Http::emptyLine()
                . Http::line('HTTP/1.0 100 Continue')
                . Http::emptyLine()
                . Http::line('HTTP/1.0 200 OK')
                . Http::emptyLine()
                . 'foobar'
        );
        $headerList = $httpResponse->headers();
        $this->assertInstanceOf('stubbles\peer\HeaderList', $headerList);
        $this->assertEquals(__METHOD__, $headerList->get('Host'));
        $this->assertEquals('HTTP/1.0 200 OK', $httpResponse->statusLine());
        $this->assertEquals(new HttpVersion(1, 0), $httpResponse->httpVersion());
        $this->assertEquals(200, $httpResponse->statusCode());
        $this->assertEquals('OK', $httpResponse->reasonPhrase());
        $this->assertEquals(Http::STATUS_CLASS_SUCCESS, $httpResponse->statusCodeClass());
        $this->assertEquals('foobar', $httpResponse->body());
    }

    /**
     * @test
     */
    public function continuesOnStatusCode102()
    {
        $httpResponse = $this->createResponse(
                Http::line('HTTP/1.0 102 Processing')
                . Http::line('Host: ' . __METHOD__)
                . Http::emptyLine()
                . Http::line('HTTP/1.0 102 Processing')
                . Http::emptyLine()
                . Http::line('HTTP/1.1 404 Not Found')
                . Http::emptyLine()
                . 'foobar'
        );
        $headerList = $httpResponse->headers();
        $this->assertInstanceOf('stubbles\peer\HeaderList', $headerList);
        $this->assertEquals(__METHOD__, $headerList->get('Host'));
        $this->assertEquals('HTTP/1.1 404 Not Found', $httpResponse->statusLine());
        $this->assertEquals(new HttpVersion(1, 1), $httpResponse->httpVersion());
        $this->assertEquals(404, $httpResponse->statusCode());
        $this->assertEquals('Not Found', $httpResponse->reasonPhrase());
        $this->assertEquals(Http::STATUS_CLASS_ERROR_CLIENT, $httpResponse->statusCodeClass());
    }

    /**
     * @test
     */
    public function illegalStatusLineLeadsToEmptyResponse()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['Illegal Response',
                         'Host: ' . __METHOD__,
                         ''
                        ]
                )
        );
        $this->assertNull($httpResponse->statusLine());
        $this->assertNull($httpResponse->httpVersion());
        $this->assertNull($httpResponse->statusCode());
        $this->assertNull($httpResponse->reasonPhrase());
        $this->assertNull($httpResponse->statusCodeClass());
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function statusLineWithInvalidHttpVersionLeadsToEmptyResponse()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['HTTP/400 102 Processing',
                         'Host: ' . __METHOD__,
                         ''
                        ]
                )
        );
        $this->assertNull($httpResponse->statusLine());
        $this->assertNull($httpResponse->httpVersion());
        $this->assertNull($httpResponse->statusCode());
        $this->assertNull($httpResponse->reasonPhrase());
        $this->assertNull($httpResponse->statusCodeClass());
    }
}
