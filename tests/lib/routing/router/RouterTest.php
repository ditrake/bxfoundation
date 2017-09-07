<?php

namespace creative\foundation\tests\lib\routing\router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testRoute()
    {
        $content = (string) mt_rand();

        $router = new \creative\foundation\routing\router\Router;

        $request = $this->getMockBuilder('\creative\foundation\request\RequestInterface')
            ->getMock();

        $response = $this->getMockBuilder('\creative\foundation\response\ResponseInterface')
            ->getMock();

        $rule1 = $this->getMockBuilder('\creative\foundation\routing\rule\RuleInterface')
            ->getMock();
        $rule1->method('parse')->will($this->returnValue(null));
        $action1 = $this->getMockBuilder('\creative\foundation\routing\action\ActionInterface')
            ->getMock();
        $action1->expects($this->never())->method('run');
        $router->registerRoute($rule1, $action1);

        $ruleResult = $this->getMockBuilder('\creative\foundation\routing\rule\RuleResultInterface')
            ->getMock();
        $rule2 = $this->getMockBuilder('\creative\foundation\routing\rule\RuleInterface')
            ->getMock();
        $rule2->method('parse')
            ->with($this->equalTo($request))
            ->will($this->returnValue($ruleResult));
        $action2 = $this->getMockBuilder('\creative\foundation\routing\action\ActionInterface')
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
        $router = new \creative\foundation\routing\router\Router;

        $request = $this->getMockBuilder('\creative\foundation\request\RequestInterface')
            ->getMock();

        $response = $this->getMockBuilder('\creative\foundation\response\ResponseInterface')
            ->getMock();

        $rule1 = $this->getMockBuilder('\creative\foundation\routing\rule\RuleInterface')
            ->getMock();
        $rule1->method('parse')->will($this->returnValue(null));
        $action1 = $this->getMockBuilder('\creative\foundation\routing\action\ActionInterface')
            ->getMock();
        $action1->expects($this->never())->method('run');
        $router->registerRoute($rule1, $action1);

        $this->setExpectedException('\creative\foundation\routing\NotFoundException');

        $router->route($request, $response);
    }

    public function testExceptionAction()
    {
        $content = (string) mt_rand();

        $router = new \creative\foundation\routing\router\Router;

        $request = $this->getMockBuilder('\creative\foundation\request\RequestInterface')
            ->getMock();

        $response = $this->getMockBuilder('\creative\foundation\response\ResponseInterface')
            ->getMock();

        $rule1 = $this->getMockBuilder('\creative\foundation\routing\rule\RuleInterface')
            ->getMock();
        $rule1->method('parse')->will($this->returnValue(null));
        $action1 = $this->getMockBuilder('\creative\foundation\routing\action\ActionInterface')
            ->getMock();
        $action1->expects($this->never())->method('run');
        $router->registerRoute($rule1, $action1);

        $actionException = $this->getMockBuilder('\creative\foundation\routing\action\ActionInterface')
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
