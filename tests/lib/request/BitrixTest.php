<?php

namespace marvin255\bxfoundation\tests\lib\request;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\request\Bitrix;
use stdClass;

class BitrixTest extends BaseCase
{
    /**
     * @test
     */
    public function testGetMethod()
    {
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestMethod'])
            ->getMock();
        $bxRequest->method('getRequestMethod')->will($this->returnValue('PUT'));

        $request = new Bitrix($bxRequest);

        $this->assertSame('PUT', $request->getMethod());
    }

    /**
     * @test
     */
    public function testGetScheme()
    {
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['isHttps'])
            ->getMock();
        $bxRequest->method('isHttps')->will($this->returnValue(true));

        $request = new Bitrix($bxRequest);

        $this->assertSame('https', $request->getScheme());
    }

    /**
     * @test
     */
    public function testGetHost()
    {
        $host = mt_rand();
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getHttpHost'])
            ->getMock();
        $bxRequest->method('getHttpHost')->will($this->returnValue($host));

        $request = new Bitrix($bxRequest);

        $this->assertSame($host, $request->getHost());
    }

    /**
     * @test
     */
    public function testGetPath()
    {
        $path = '/' . mt_rand() . '/';
        $uri = $path . '?' . mt_rand() . '=' . mt_rand();
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestUri'])
            ->getMock();
        $bxRequest->method('getRequestUri')->will($this->returnValue($uri));

        $request = new Bitrix($bxRequest);

        $this->assertSame($path, $request->getPath());
    }

    /**
     * @test
     */
    public function testGetQueryData()
    {
        $data = ['test1' => mt_rand(), 'test2' => mt_rand()];

        $listMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['toArray'])
            ->getMock();
        $listMock->method('toArray')->will($this->returnValue($data));

        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestMethod', 'getQueryList'])
            ->getMock();
        $bxRequest->method('getRequestMethod')->will($this->returnValue('GET'));
        $bxRequest->method('getQueryList')->will($this->returnValue($listMock));

        $request = new Bitrix($bxRequest);

        $this->assertSame($data, $request->getData());
    }

    /**
     * @test
     */
    public function testGetPostData()
    {
        $data = ['test1' => mt_rand(), 'test2' => mt_rand()];

        $listMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['toArray'])
            ->getMock();
        $listMock->method('toArray')->will($this->returnValue($data));

        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestMethod', 'getPostList'])
            ->getMock();
        $bxRequest->method('getRequestMethod')->will($this->returnValue('POST'));
        $bxRequest->method('getPostList')->will($this->returnValue($listMock));

        $request = new Bitrix($bxRequest);

        $this->assertSame($data, $request->getData());
    }

    /**
     * @test
     */
    public function testGetInputData()
    {
        $data = 'test=1&test2=2';

        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRequestMethod'])
            ->getMock();
        $bxRequest->method('getRequestMethod')->will($this->returnValue('PUT'));

        $request = $this->getMockBuilder(Bitrix::class)
            ->setConstructorArgs([$bxRequest])
            ->setMethods(['getPhpInputData'])
            ->getMock();
        $request->method('getPhpInputData')->will($this->returnValue($data));

        $this->assertSame(['test' => '1', 'test2' => '2'], $request->getData());
    }

    /**
     * @test
     */
    public function testGetHeadersFromServer()
    {
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')->getMock();
        $request = new Bitrix($bxRequest);

        $oldServer = $_SERVER;
        $_SERVER = [
            'HTTP_TEST_HEADER' => 'test_value',
            'HTTP_TEST_HEADER_2' => 'test value 2',
            'REQUEST_URI' => 'test',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ];

        $this->assertSame(
            [
                'test-header' => 'test_value',
                'test-header-2' => 'test value 2',
                'x-requested-with' => 'XMLHttpRequest',
            ],
            $request->getHeaders()
        );

        $this->assertSame(
            'test value 2',
            $request->getHeader('test-header-2')
        );

        $_SERVER = $oldServer;
    }

    /**
     * @test
     */
    public function testGetCookie()
    {
        $cookie = ['test1' => mt_rand(), 'test2' => mt_rand()];

        $listMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['toArray'])
            ->getMock();
        $listMock->method('toArray')->will($this->returnValue($cookie));

        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getCookieList'])
            ->getMock();
        $bxRequest->method('getCookieList')->will($this->returnValue($listMock));

        $request = new Bitrix($bxRequest);

        $this->assertSame($cookie, $request->getCookie());
    }

    /**
     * @test
     */
    public function testGetRemoteAddress()
    {
        $remote = mt_rand();
        $bxRequest = $this->getMockBuilder('\Bitrix\Main\HttpRequest')
            ->setMethods(['getRemoteAddress'])
            ->getMock();
        $bxRequest->method('getRemoteAddress')->will($this->returnValue($remote));

        $request = new Bitrix($bxRequest);

        $this->assertSame($remote, $request->getRemoteAddress());
    }
}
