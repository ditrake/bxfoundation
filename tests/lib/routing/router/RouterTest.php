<?php

namespace marvin255\bxfoundation\tests\lib\routing\router;

class RouterTest extends \marvin255\bxfoundation\tests\BaseCase
{
    public function testRoute()
    {
        $content = (string) mt_rand();

        $router = new \marvin255\bxfoundation\routing\router\Router;

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\RequestInterface')
            ->getMock();

        $response = $this->getMockBuilder('\marvin255\bxfoundation\response\ResponseInterface')
            ->getMock();

        $rule1 = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\RuleInterface')
            ->getMock();
        $rule1->method('parse')->will($this->returnValue(null));
        $action1 = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $action1->expects($this->never())->method('run');
        $router->registerRoute($rule1, $action1);

        $ruleResult = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\RuleResultInterface')
            ->getMock();
        $rule2 = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\RuleInterface')
            ->getMock();
        $rule2->method('parse')
            ->with($this->equalTo($request))
            ->will($this->returnValue($ruleResult));
        $action2 = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $action2->expects($this->once())
            ->method('run')
            ->with($this->equalTo($ruleResult), $this->equalTo($request), $this->equalTo($response))
            ->will($this->returnValue($content));
        $router->registerRoute($rule2, $action2);

        $this->assertSame(
            $content,
            $router->route($request, $response),
            'router must returns data from action'
        );
    }

    public function testRouteNotFoundException()
    {
        $router = new \marvin255\bxfoundation\routing\router\Router;

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\RequestInterface')
            ->getMock();

        $response = $this->getMockBuilder('\marvin255\bxfoundation\response\ResponseInterface')
            ->getMock();

        $rule1 = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\RuleInterface')
            ->getMock();
        $rule1->method('parse')->will($this->returnValue(null));
        $action1 = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $action1->expects($this->never())->method('run');
        $router->registerRoute($rule1, $action1);

        $this->setExpectedException('\marvin255\bxfoundation\routing\NotFoundException');

        $router->route($request, $response);
    }

    public function testExceptionAction()
    {
        $content = (string) mt_rand();

        $router = new \marvin255\bxfoundation\routing\router\Router;

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\RequestInterface')
            ->getMock();

        $response = $this->getMockBuilder('\marvin255\bxfoundation\response\ResponseInterface')
            ->getMock();

        $rule1 = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\RuleInterface')
            ->getMock();
        $rule1->method('parse')->will($this->returnValue(null));
        $action1 = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $action1->expects($this->never())->method('run');
        $router->registerRoute($rule1, $action1);

        $actionException = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $actionException->expects($this->once())
            ->method('run')
            ->will($this->returnValue($content));
        $router->registerRouteException(404, $actionException);

        $this->assertSame(
            $content,
            $router->route($request, $response),
            'router must returns data from exception action on exception'
        );
    }
}
