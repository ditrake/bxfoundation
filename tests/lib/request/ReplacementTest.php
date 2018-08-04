<?php

namespace marvin255\bxfoundation\tests\lib\request;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\request\Replacement;

class ReplacementTest extends BaseCase
{
    /**
     * @test
     */
    public function testGetMethod()
    {
        $requestData = 'request_data_' . mt_rand();
        $replaceData = 'replace_data_' . mt_rand();

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->method('getMethod')->will($this->returnValue($requestData));

        $replacementRequest = new Replacement($request);
        $replacementReplacement = new Replacement($request, ['method' => $replaceData]);

        $this->assertSame($requestData, $replacementRequest->getMethod());
        $this->assertSame($replaceData, $replacementReplacement->getMethod());
    }

    /*
     * @test
     */
    public function testGetScheme()
    {
        $requestData = 'request_data_' . mt_rand();
        $replaceData = 'replace_data_' . mt_rand();

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->method('getScheme')->will($this->returnValue($requestData));

        $replacementRequest = new Replacement($request);
        $replacementReplacement = new Replacement($request, ['scheme' => $replaceData]);

        $this->assertSame($requestData, $replacementRequest->getScheme());
        $this->assertSame($replaceData, $replacementReplacement->getScheme());
    }

    /**
     * @test
     */
    public function testGetHost()
    {
        $requestData = 'request_data_' . mt_rand();
        $replaceData = 'replace_data_' . mt_rand();

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->method('getHost')->will($this->returnValue($requestData));

        $replacementRequest = new Replacement($request);
        $replacementReplacement = new Replacement($request, ['host' => $replaceData]);

        $this->assertSame($requestData, $replacementRequest->getHost());
        $this->assertSame($replaceData, $replacementReplacement->getHost());
    }

    /**
     * @test
     */
    public function testGetPath()
    {
        $requestData = 'request_data_' . mt_rand();
        $replaceData = 'replace_data_' . mt_rand();

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->method('getPath')->will($this->returnValue($requestData));

        $replacementRequest = new Replacement($request);
        $replacementReplacement = new Replacement($request, ['path' => $replaceData]);

        $this->assertSame($requestData, $replacementRequest->getPath());
        $this->assertSame($replaceData, $replacementReplacement->getPath());
    }

    /**
     * @test
     */
    public function testGetData()
    {
        $requestData = 'request_data_' . mt_rand();
        $replaceData = 'replace_data_' . mt_rand();

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->method('getData')->will($this->returnValue($requestData));

        $replacementRequest = new Replacement($request);
        $replacementReplacement = new Replacement($request, ['data' => $replaceData]);

        $this->assertSame($requestData, $replacementRequest->getData());
        $this->assertSame($replaceData, $replacementReplacement->getData());
    }

    /**
     * @test
     */
    public function testGetCookie()
    {
        $requestData = 'request_data_' . mt_rand();
        $replaceData = 'replace_data_' . mt_rand();

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->method('getCookie')->will($this->returnValue($requestData));

        $replacementRequest = new Replacement($request);
        $replacementReplacement = new Replacement($request, ['cookie' => $replaceData]);

        $this->assertSame($requestData, $replacementRequest->getCookie());
        $this->assertSame($replaceData, $replacementReplacement->getCookie());
    }

    /**
     * @test
     */
    public function testGetHeader()
    {
        $requestData = 'request_data_' . mt_rand();
        $replaceData = 'replace_data_' . mt_rand();

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->method('getHeaders')->will($this->returnValue([
            'test' => $requestData,
        ]));

        $replacementRequest = new Replacement($request);
        $replacementReplacement = new Replacement($request, [
            'headers' => [
                'test' => $replaceData,
            ],
        ]);

        $this->assertSame($requestData, $replacementRequest->getHeader('test'));
        $this->assertSame($replaceData, $replacementReplacement->getHeader('test'));
    }

    /**
     * @test
     */
    public function testGetRemoteAddress()
    {
        $requestData = 'request_data_' . mt_rand();
        $replaceData = 'replace_data_' . mt_rand();

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->method('getRemoteAddress')->will($this->returnValue($requestData));

        $replacementRequest = new Replacement($request);
        $replacementReplacement = new Replacement($request, ['remoteAddress' => $replaceData]);

        $this->assertSame($requestData, $replacementRequest->getRemoteAddress());
        $this->assertSame($replaceData, $replacementReplacement->getRemoteAddress());
    }
}
