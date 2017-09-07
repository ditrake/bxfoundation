<?php

namespace creative\foundation\tests\lib\request;

class BitrixTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMethod()
    {
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestMethod'])
            ->getMock();
        $bxRequest->method('getRequestMethod')
            ->will($this->returnValue('PUT'));

        $request = new \creative\foundation\request\Bitrix($bxRequest);

        $this->assertSame(
            'PUT',
            $request->getMethod(),
            'getMethod method must returns method from \Bitrix\Main\HttpRequest'
        );
    }

    public function testGetScheme()
    {
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['isHttps'])
            ->getMock();
        $bxRequest->method('isHttps')
            ->will($this->returnValue(true));

        $request = new \creative\foundation\request\Bitrix($bxRequest);

        $this->assertSame(
            'https',
            $request->getScheme(),
            'getScheme method must returns scheme from \Bitrix\Main\HttpRequest'
        );
    }

    public function testGetHost()
    {
        $host = mt_rand();
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getHttpHost'])
            ->getMock();
        $bxRequest->method('getHttpHost')
            ->will($this->returnValue($host));

        $request = new \creative\foundation\request\Bitrix($bxRequest);

        $this->assertSame(
            $host,
            $request->getHost(),
            'getHost method must returns host from \Bitrix\Main\HttpRequest'
        );
    }

    public function testGetPath()
    {
        $path = '/' . mt_rand() . '/';
        $uri = $path . '?' . mt_rand() . '=' . mt_rand();
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestUri'])
            ->getMock();
        $bxRequest->method('getRequestUri')
            ->will($this->returnValue($uri));

        $request = new \creative\foundation\request\Bitrix($bxRequest);

        $this->assertSame(
            $path,
            $request->getPath(),
            'getPath method must returns path from \Bitrix\Main\HttpRequest'
        );
    }

    public function testGetQueryData()
    {
        $data = ['test1' => mt_rand(), 'test2' => mt_rand()];

        $listMock = $this->getMockBuilder('\StdClass')
            ->setMethods(['toArray'])
            ->getMock();
        $listMock->method('toArray')
            ->will($this->returnValue($data));

        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestMethod', 'getQueryList'])
            ->getMock();
        $bxRequest->method('getRequestMethod')
            ->will($this->returnValue('GET'));
        $bxRequest->method('getQueryList')
            ->will($this->returnValue($listMock));

        $request = new \creative\foundation\request\Bitrix($bxRequest);

        $this->assertSame(
            $data,
            $request->getData(),
            'getData method must returns query data for GET method from \Bitrix\Main\HttpRequest'
        );
    }

    public function testGetPostData()
    {
        $data = ['test1' => mt_rand(), 'test2' => mt_rand()];

        $listMock = $this->getMockBuilder('\StdClass')
            ->setMethods(['toArray'])
            ->getMock();
        $listMock->method('toArray')
            ->will($this->returnValue($data));

        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestMethod', 'getPostList'])
            ->getMock();
        $bxRequest->method('getRequestMethod')
            ->will($this->returnValue('POST'));
        $bxRequest->method('getPostList')
            ->will($this->returnValue($listMock));

        $request = new \creative\foundation\request\Bitrix($bxRequest);

        $this->assertSame(
            $data,
            $request->getData(),
            'getData method must returns post data for POST method from \Bitrix\Main\HttpRequest'
        );
    }

    public function testGetInputData()
    {
        $data = 'test=1&test2=2';

        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestMethod'])
            ->getMock();
        $bxRequest->method('getRequestMethod')
            ->will($this->returnValue('PUT'));

        $request = $this->getMock(
            '\creative\foundation\request\Bitrix',
            ['getPhpInputData'],
            [$bxRequest],
            '',
            true
        );
        $request->method('getPhpInputData')
            ->will($this->returnValue($data));

        $this->assertSame(
            ['test' => '1', 'test2' => '2'],
            $request->getData(),
            'getData method must returns post data for POST method from \Bitrix\Main\HttpRequest'
        );
    }

    public function testGetHeadersFromServer()
    {
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')->getMock();
        $request = new \creative\foundation\request\Bitrix($bxRequest);

        $oldServer = $_SERVER;
        $_SERVER = [
            'HTTP_TEST_HEADER' => 'test_value',
            'HTTP_TEST_HEADER_2' => 'test value 2',
            'REQUEST_URI' => 'test',
        ];

        $this->assertSame(
            ['TestHeader' => 'test_value', 'TestHeader2' => 'test value 2'],
            $request->getHeaders(),
            'getHeaders method must parse headers from $_SERVER'
        );

        $this->assertSame(
            'test value 2',
            $request->getHeader('TestHeader2'),
            'getHeader method must returns header by it\'s name'
        );

        $_SERVER = $oldServer;
    }

    public function testGetCookie()
    {
        $cookie = ['test1' => mt_rand(), 'test2' => mt_rand()];

        $listMock = $this->getMockBuilder('\StdClass')
            ->setMethods(['toArray'])
            ->getMock();
        $listMock->method('toArray')
            ->will($this->returnValue($cookie));

        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getCookieList'])
            ->getMock();
        $bxRequest->method('getCookieList')
            ->will($this->returnValue($listMock));

        $request = new \creative\foundation\request\Bitrix($bxRequest);

        $this->assertSame(
            $cookie,
            $request->getCookie(),
            'getCookie method must returns cookie data from \Bitrix\Main\HttpRequest'
        );
    }

    public function testGetRemoteAddress()
    {
        $remote = mt_rand();
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRemoteAddress'])
            ->getMock();
        $bxRequest->method('getRemoteAddress')
            ->will($this->returnValue($remote));

        $request = new \creative\foundation\request\Bitrix($bxRequest);

        $this->assertSame(
            $remote,
            $request->getRemoteAddress(),
            'getRemoteAddress method must returns ip from \Bitrix\Main\HttpRequest'
        );
    }
}
