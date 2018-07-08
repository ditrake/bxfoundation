<?php

namespace marvin255\bxfoundation\tests\lib\routing\router;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\routing\router\Router;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\routing\rule\RuleInterface;
use marvin255\bxfoundation\routing\action\ActionInterface;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;
use marvin255\bxfoundation\response\exception\NotFound;

class RouterTest extends BaseCase
{
    /**
     * @test
     */
    public function testRoute()
    {
        $content = (string) mt_rand();

        $router = new Router;

        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $rule1 = $this->getMockBuilder(RuleInterface::class)
            ->getMock();
        $rule1->method('parse')->will($this->returnValue(null));
        $action1 = $this->getMockBuilder(ActionInterface::class)
            ->getMock();
        $action1->expects($this->never())->method('run');
        $router->registerRoute($rule1, $action1);

        $ruleResult = $this->getMockBuilder(RuleResultInterface::class)
            ->getMock();
        $rule2 = $this->getMockBuilder(RuleInterface::class)
            ->getMock();
        $rule2->method('parse')
            ->with($this->equalTo($request))
            ->will($this->returnValue($ruleResult));
        $action2 = $this->getMockBuilder(ActionInterface::class)
            ->getMock();
        $action2->expects($this->once())
            ->method('run')
            ->with($this->equalTo($ruleResult), $this->equalTo($request), $this->equalTo($response))
            ->will($this->returnValue($content));
        $router->registerRoute($rule2, $action2);

        $this->assertSame($content, $router->route($request, $response));
    }

    /**
     * @test
     */
    public function testRouteNotFoundException()
    {
        $router = new Router;

        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $rule1 = $this->getMockBuilder(RuleInterface::class)
            ->getMock();
        $rule1->method('parse')->will($this->returnValue(null));
        $action1 = $this->getMockBuilder(ActionInterface::class)
            ->getMock();
        $action1->expects($this->never())->method('run');
        $router->registerRoute($rule1, $action1);

        $this->setExpectedException(NotFound::class);

        $router->route($request, $response);
    }

    /**
     * @test
     */
    public function testExceptionAction()
    {
        $content = (string) mt_rand();

        $router = new Router;

        $request = $this->getMockBuilder(RequestInterface::class)
            ->getMock();

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $rule1 = $this->getMockBuilder(RuleInterface::class)
            ->getMock();
        $rule1->method('parse')->will($this->returnValue(null));
        $action1 = $this->getMockBuilder(ActionInterface::class)
            ->getMock();
        $action1->expects($this->never())->method('run');
        $router->registerRoute($rule1, $action1);

        $actionException = $this->getMockBuilder(ActionInterface::class)
            ->getMock();
        $actionException->expects($this->once())
            ->method('run')
            ->will($this->returnValue($content));
        $router->registerExceptionAction(404, $actionException);

        $this->assertSame($content, $router->route($request, $response));
    }
}
