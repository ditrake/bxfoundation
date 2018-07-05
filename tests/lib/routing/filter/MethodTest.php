<?php

namespace marvin255\bxfoundation\tests\lib\routing\filter;

class MethodTest extends \marvin255\bxfoundation\tests\BaseCase
{
    public function testEmptyConstructorException()
    {
        $this->setExpectedException('\marvin255\bxfoundation\routing\Exception');
        $filter = new \marvin255\bxfoundation\routing\filter\Method([]);
    }

    public function testAttachTo()
    {
        $filter = new \marvin255\bxfoundation\routing\filter\Method(['POST']);

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
        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\RequestInterface')
            ->getMock();
        $request->method('getMethod')
            ->will($this->returnValue('PUT'));

        $result = $this->getMockBuilder('\marvin255\bxfoundation\events\ResultInterface')
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->once())
            ->method('fail');

        $filter = new \marvin255\bxfoundation\routing\filter\Method(['POST', 'DELETE']);
        $filter->filter($result);
    }
}
