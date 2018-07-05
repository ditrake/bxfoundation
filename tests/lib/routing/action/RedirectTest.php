<?php

namespace marvin255\bxfoundation\tests\lib\routing\action;

class RedirectTest extends \marvin255\bxfoundation\tests\BaseCase
{
    public function testConstructorEmptyUrlException()
    {
        $this->setExpectedException('\marvin255\bxfoundation\routing\Exception');
        new \marvin255\bxfoundation\routing\action\Redirect(null);
    }

    public function testRun()
    {
        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder('\marvin255\bxfoundation\response\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $ruleResult = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\RuleResultInterface')
            ->getMock();

        $url = '/' . mt_rand() . '/';
        $action = $this->getMock(
            '\marvin255\bxfoundation\routing\action\Redirect',
            ['localRedirect'],
            [$url],
            '',
            true
        );
        $action->expects($this->once())
            ->method('localRedirect')
            ->with($this->equalTo($url));

        $action->run($ruleResult, $request, $response);
    }
}
