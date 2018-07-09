<?php

namespace marvin255\bxfoundation\tests\lib\routing\action;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\routing\action\Component;
use marvin255\bxfoundation\request\Bitrix as Request;
use marvin255\bxfoundation\response\Bitrix as Response;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;
use stdClass;

class ComponentTest extends BaseCase
{
    /**
     * @test
     */
    public function testConstructorEmptyComponentException()
    {
        $this->setExpectedException(Exception::class);
        new Component('');
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

        $testParam = mt_rand();
        $ruleResult = $this->getMockBuilder(RuleResultInterface::class)
            ->getMock();
        $ruleResult->method('getParam')
            ->with($this->equalTo('TEST'))
            ->will($this->returnValue($testParam));

        $content = (string) mt_rand();
        global $APPLICATION;
        $APPLICATION = $this->getMockBuilder(stdClass::class)
            ->setMethods(['IncludeComponent'])
            ->getMock();
        $APPLICATION->expects($this->once())
            ->method('IncludeComponent')
            ->with(
                $this->equalTo('component'),
                $this->equalTo('template'),
                ['test' => $testParam, 'test2' => 'test2']
            )
            ->will($this->returnCallback(function () use ($content) {
                echo $content;
            }));

        $action = new Component(
            'component',
            'template',
            ['test' => '$ruleResult.TEST', 'test2' => 'test2']
        );

        $this->assertSame(
            $content,
            $action->run($ruleResult, $request, $response)
        );
    }
}
