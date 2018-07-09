<?php

namespace marvin255\bxfoundation\tests\lib\routing\action;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\routing\action\Redirect;
use marvin255\bxfoundation\request\Bitrix as Request;
use marvin255\bxfoundation\response\Bitrix as Response;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;

class RedirectTest extends BaseCase
{
    /**
     * @test
     */
    public function testConstructorEmptyUrlException()
    {
        $this->setExpectedException(Exception::class);
        new Redirect(null);
    }

    /**
     * @test
     */
    public function testRun()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();
        $ruleResult = $this->getMockBuilder(RuleResultInterface::class)
            ->getMock();

        $url = '/' . mt_rand() . '/';
        $action = $this->getMockBuilder(Redirect::class)
            ->setConstructorArgs([$url])
            ->setMethods(['localRedirect'])
            ->getMock();
        $action->expects($this->once())
            ->method('localRedirect')
            ->with($this->equalTo($url));

        $action->run($ruleResult, $request, $response);
    }
}
