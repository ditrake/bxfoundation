<?php

namespace marvin255\bxfoundation\tests\lib\routing\filter;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\routing\filter\GetOnly;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\events\ResultInterface;

class GetOnlyTest extends BaseCase
{
    /**
     * @test
     */
    public function testFilterFalse()
    {
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $request->method('getMethod')->will($this->returnValue('PUT'));

        $result = $this->getMockBuilder(ResultInterface::class)
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->once())
            ->method('fail');

        $filter = new GetOnly;
        $filter->filter($result);
    }

    /**
     * @test
     */
    public function testFilterTrue()
    {
        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();
        $request->method('getMethod')->will($this->returnValue('GET'));

        $result = $this->getMockBuilder(ResultInterface::class)
            ->getMock();
        $result->method('getParam')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));
        $result->expects($this->never())
            ->method('fail');

        $filter = new GetOnly;
        $filter->filter($result);
    }
}
