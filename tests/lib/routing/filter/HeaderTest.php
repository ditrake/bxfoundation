<?php

namespace marvin255\bxfoundation\tests\lib\routing\filter;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyConstructorException()
    {
        $this->setExpectedException('\marvin255\bxfoundation\routing\Exception');
        $filter = new \marvin255\bxfoundation\routing\filter\Header([]);
    }

    public function testAttachTo()
    {
        $filter = new \marvin255\bxfoundation\routing\filter\Header(['test' => 'test']);

        $eventable = $this->getMockBuilder('\marvin255\bxfoundation\events\EventableInterface')
            ->getMock();
        $eventable->expects($this->once())
            ->method('attachEventCallback')
            ->with(
                $this->equalTo('onBeforeRouteParsing'),
                $this->equalTo([$filter, 'filter'])
            );

        $this->assertSame(
            $filter,
            $filter->attachTo($eventable),
            'attachTo method must return it\'s object'
        );
    }

    public function testFilter()
    {
        $name = (string) mt_rand();
        $value = (string) mt_rand();

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\RequestInterface')
            ->getMock();
        $request->method('getHeader')
            ->with($this->equalTo($name))
            ->will($this->returnValue(mt_rand()));

        $result = $this->getMockBuilder('\marvin255\bxfoundation\events\ResultInterface')
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->once())
            ->method('fail');

        $filter = new \marvin255\bxfoundation\routing\filter\Header([$name => $value]);
        $filter->filter($result);
    }
}
