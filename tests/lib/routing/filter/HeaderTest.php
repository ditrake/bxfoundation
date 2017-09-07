<?php

namespace creative\foundation\tests\lib\routing\filter;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyConstructorException()
    {
        $this->setExpectedException('\creative\foundation\routing\Exception');
        $filter = new \creative\foundation\routing\filter\Header([]);
    }

    public function testAttachTo()
    {
        $filter = new \creative\foundation\routing\filter\Header(['test' => 'test']);

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
        $name = (string) mt_rand();
        $value = (string) mt_rand();

        $request = $this->getMockBuilder('\creative\foundation\request\RequestInterface')
            ->getMock();
        $request->method('getHeader')
            ->with($this->equalTo($name))
            ->will($this->returnValue(mt_rand()));

        $result = $this->getMockBuilder('\creative\foundation\events\ResultInterface')
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->once())
            ->method('fail');

        $filter = new \creative\foundation\routing\filter\Header([$name => $value]);
        $filter->filter($result);
    }
}
