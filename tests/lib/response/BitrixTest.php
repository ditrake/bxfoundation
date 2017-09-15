<?php

namespace marvin255\bxfoundation\tests\lib\response;

class BitrixTest extends \PHPUnit_Framework_TestCase
{
    public function testSetHeader()
    {
        $name = mt_rand();
        $value = mt_rand();

        $bxResponse = $this->getMockBuilder('\Bitrix\Main\HttpResponse')
            ->setMethods(['addHeader'])
            ->getMock();
        $bxResponse->expects($this->once())
            ->method('addHeader')
            ->with($this->equalTo($name), $this->equalTo($value));

        $response = new \marvin255\bxfoundation\response\Bitrix($bxResponse);

        $this->assertSame(
            $response,
            $response->setHeader($name, $value),
            'setHeader method must returns it\'s object'
        );

        $this->assertSame(
            $value,
            $response->getHeader($name),
            'getHeader method must gets header value by it\'s name'
        );
    }

    public function testSetStatus()
    {
        $status = mt_rand();

        $bxResponse = $this->getMockBuilder('\Bitrix\Main\HttpResponse')
            ->setMethods(['setStatus'])
            ->getMock();
        $bxResponse->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo($status));

        $response = new \marvin255\bxfoundation\response\Bitrix($bxResponse);

        $this->assertSame(
            $response,
            $response->setStatus($status),
            'setStatus method must returns it\'s object'
        );

        $this->assertSame(
            $status,
            $response->getStatus(),
            'getStatus method must gets status setted by setStatus'
        );
    }
}
