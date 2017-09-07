<?php

namespace creative\foundation\tests\lib\routing\filter;

class MethodTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyConstructorException()
    {
        $this->setExpectedException('\creative\foundation\routing\Exception');
        $filter = new \creative\foundation\routing\filter\Method([]);
    }

    public function testAttachTo()
    {
        $filter = new \creative\foundation\routing\filter\Method(['POST']);

        $eventable = $this->getMockBuilder('\creative\foundation\events\EventableInterface')
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
        $request = $this->getMockBuilder('\creative\foundation\request\RequestInterface')
            ->getMock();
        $request->method('getMethod')
            ->will($this->returnValue('PUT'));

        $result = $this->getMockBuilder('\creative\foundation\events\ResultInterface')
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->once())
            ->method('fail');

        $filter = new \creative\foundation\routing\filter\Method(['POST', 'DELETE']);
        $filter->filter($result);
    }
}
