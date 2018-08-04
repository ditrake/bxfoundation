<?php

namespace marvin255\bxfoundation\tests\lib\routing\filter;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\routing\filter\AjaxOnly;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\events\ResultInterface;

class AjaxOnlyTest extends BaseCase
{
    /**
     * @test
     */
    public function testFilterFalse()
    {
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $request->method('getHeader')->will($this->returnValue(null));

        $result = $this->getMockBuilder(ResultInterface::class)
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->once())
            ->method('fail');

        $filter = new AjaxOnly;
        $filter->filter($result);
    }

    /**
     * @test
     */
    public function testFilterTrue()
    {
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $request->method('getHeader')
            ->with($this->equalTo('x-requested-with'))
            ->will($this->returnValue('XMLHttpRequest'));

        $result = $this->getMockBuilder(ResultInterface::class)
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->never())
            ->method('fail');

        $filter = new AjaxOnly;
        $filter->filter($result);
    }
}
