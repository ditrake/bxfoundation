<?php

namespace marvin255\bxfoundation\tests\lib\routing\action;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\routing\action\Chain;
use marvin255\bxfoundation\request\Bitrix as Request;
use marvin255\bxfoundation\response\Bitrix as Response;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;
use marvin255\bxfoundation\routing\action\ActionInterface;

class ChainTest extends BaseCase
{
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

        $content1 = (string) mt_rand();
        $action1 = $this->getMockBuilder(ActionInterface::class)
            ->getMock();
        $action1->method('run')->will($this->returnValue($content1));

        $content2 = (string) mt_rand();
        $action2 = $this->getMockBuilder(ActionInterface::class)
            ->getMock();
        $action2->method('run')->will($this->returnValue($content2));

        $chain = (new Chain([$action1]))->chain($action2);

        $this->assertSame(
            $content1 . $content2,
            $chain->run($ruleResult, $request, $response)
        );
    }
}
