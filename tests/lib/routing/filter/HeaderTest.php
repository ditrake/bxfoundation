<?php

namespace marvin255\bxfoundation\tests\lib\routing\filter;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\routing\filter\Header;
use marvin255\bxfoundation\events\EventableInterface;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\events\ResultInterface;

class HeaderTest extends BaseCase
{
    /**
     * @test
     */
    public function testEmptyConstructorException()
    {
        $this->setExpectedException(Exception::class);
        $filter = new Header([]);
    }

    /**
     * @test
     */
    public function testAttachTo()
    {
        $filter = new Header(['test' => 'test']);

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
        $name = (string) mt_rand();
        $value = (string) mt_rand();

        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $request->method('getHeader')
            ->with($this->equalTo($name))
            ->will($this->returnValue($value . mt_rand()));

        $result = $this->getMockBuilder(ResultInterface::class)
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->once())
            ->method('fail');

        $filter = new Header([$name => $value]);
        $filter->filter($result);
    }
}
