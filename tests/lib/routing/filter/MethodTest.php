<?php

namespace marvin255\bxfoundation\tests\lib\routing\filter;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\routing\filter\Method;
use marvin255\bxfoundation\events\EventableInterface;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\events\ResultInterface;

class MethodTest extends BaseCase
{
    /**
     * @test
     */
    public function testEmptyConstructorException()
    {
        $this->setExpectedException(Exception::class);
        $filter = new Method([]);
    }

    /**
     * @test
     */
    public function testAttachTo()
    {
        $filter = new Method(['POST']);

        $eventable = $this->getMockBuilder(EventableInterface::class)
            ->getMock();
        $eventable->expects($this->once())
            ->method('attachEventCallback')
            ->with(
                $this->equalTo('onBeforeRouteParsing'),
                $this->equalTo([$filter, 'filter'])
            );

        $this->assertSame($filter, $filter->attachTo($eventable));
    }

    /**
     * @test
     */
    public function testFilter()
    {
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $request->method('getMethod')
            ->will($this->returnValue('PUT'));

        $result = $this->getMockBuilder(ResultInterface::class)
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->once())
            ->method('fail');

        $filter = new Method(['POST', 'DELETE']);
        $filter->filter($result);
    }
}
