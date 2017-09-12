<?php

namespace creative\foundation\tests\lib\routing\action;

class RedirectTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorEmptyUrlException()
    {
        $this->setExpectedException('\creative\foundation\routing\Exception');
        new \creative\foundation\routing\action\Redirect(null);
    }

    public function testRun()
    {
        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder('\creative\foundation\response\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $ruleResult = $this->getMockBuilder('\creative\foundation\routing\rule\RuleResultInterface')
            ->getMock();

        $url = '/' . mt_rand() . '/';
        $action = $this->getMock(
            '\creative\foundation\routing\action\Redirect',
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
