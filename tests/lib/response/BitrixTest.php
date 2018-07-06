<?php

namespace marvin255\bxfoundation\tests\lib\response;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\response\Bitrix;

class BitrixTest extends BaseCase
{
    /**
     * @test
     */
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

        $response = new Bitrix($bxResponse);

        $this->assertSame($response, $response->setHeader($name, $value));
        $this->assertSame($value, $response->getHeader($name));
    }

    /**
     * @test
     */
    public function testGetHeaders()
    {
        $name1 = 'name_1_' . mt_rand();
        $value1 = 'value_1_' . mt_rand();
        $name2 = 'name_2_' . mt_rand();
        $value2 = 'value_2_' . mt_rand();
        $res = [
            $name1 => $value1,
            $name2 => $value2,
        ];

        $bxResponse = $this->getMockBuilder('\Bitrix\Main\HttpResponse')
            ->setMethods(['addHeader'])
            ->getMock();

        $response = $this->getMockBuilder(Bitrix::class)
            ->setConstructorArgs([$bxResponse])
            ->setMethods(['getSentHeaders'])
            ->getMock();
        $response->method('getSentHeaders')->will($this->returnValue([
            "{$name2}: {$value2}",
        ]));
        $response->setHeader($name1, $value1);
        $responseHeaders = $response->getHeaders();
        ksort($responseHeaders);

        $this->assertSame($res, $responseHeaders);
    }

    /**
     * @test
     */
    public function testSetStatus()
    {
        $status = mt_rand();

        $bxResponse = $this->getMockBuilder('\Bitrix\Main\HttpResponse')
            ->setMethods(['setStatus'])
            ->getMock();
        $bxResponse->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo($status));

        $response = new Bitrix($bxResponse);

        $this->assertSame($response, $response->setStatus($status));
        $this->assertSame($status, $response->getStatus());
    }
}
