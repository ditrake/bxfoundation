<?php

namespace marvin255\bxfoundation\tests\lib\routing\action;

class ComponentTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorEmptyComponentException()
    {
        $this->setExpectedException('\marvin255\bxfoundation\routing\Exception');
        new \marvin255\bxfoundation\routing\action\Component('');
    }

    public function testRun()
    {
        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder('\marvin255\bxfoundation\response\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();

        $testParam = mt_rand();
        $ruleResult = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\RuleResultInterface')
            ->getMock();
        $ruleResult->method('getParam')
            ->with($this->equalTo('TEST'))
            ->will($this->returnValue($testParam));

        $content = (string) mt_rand();
        global $APPLICATION;
        $APPLICATION = $this->getMockBuilder('\StdClass')
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

        $action = new \marvin255\bxfoundation\routing\action\Component(
            'component',
            'template',
            ['test' => '$ruleResult.TEST', 'test2' => 'test2']
        );

        $this->assertSame(
            $content,
            $action->run($ruleResult, $request, $response),
            'run method must build data from component'
        );
    }

    public function testOnBeforeActionRun()
    {
        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder('\marvin255\bxfoundation\response\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $ruleResult = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\RuleResultInterface')
            ->getMock();

        $action = new \marvin255\bxfoundation\routing\action\Component('component');
        $action->attachEventCallback('onBeforeActionRun', function ($eventResult) use ($action) {
            if ($action === $eventResult->getTarget()) {
                $eventResult->fail();
            }
        });

        $this->assertSame(
            null,
            $action->run($ruleResult, $request, $response),
            'run method must rises onBeforeActionRun event'
        );
    }

    public function testOnAfterActionRun()
    {
        $content = (string) mt_rand();

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder('\marvin255\bxfoundation\response\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $ruleResult = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\RuleResultInterface')
            ->getMock();

        $content = (string) mt_rand();
        global $APPLICATION;
        $APPLICATION = $this->getMockBuilder('\StdClass')
            ->setMethods(['IncludeComponent'])
            ->getMock();
        $APPLICATION->expects($this->once())
            ->method('IncludeComponent')
            ->with(
                $this->equalTo('component'),
                $this->equalTo('template'),
                ['test2' => 'test2']
            )
            ->will($this->returnCallback(function () {
                echo mt_rand();
            }));

        $action = new \marvin255\bxfoundation\routing\action\Component(
            'component',
            'template',
            ['test2' => 'test2']
        );

        $content = (string) mt_rand();
        $action->attachEventCallback('onAfterActionRun', function ($eventResult) use ($content) {
            $eventResult->setParam('return', $content);
        });

        $this->assertSame(
            $content,
            $action->run($ruleResult, $request, $response),
            'run method must rises onAfterActionRun event'
        );
    }

    public function testAttachEventCallbackEmptyNameException()
    {
        $action = new \marvin255\bxfoundation\routing\action\Component('component');

        $this->setExpectedException('\marvin255\bxfoundation\events\Exception');
        $action->attachEventCallback(null, function () {});
    }

    public function testAttachEventCallbackEmptyCallbackException()
    {
        $action = new \marvin255\bxfoundation\routing\action\Component('component');

        $this->setExpectedException('\marvin255\bxfoundation\events\Exception');
        $action->attachEventCallback('test', 123);
    }

    public function testAttachEventCallbackDuplicateException()
    {
        $action = new \marvin255\bxfoundation\routing\action\Component('component');

        $callback1 = function () {};
        $callback2 = function () {};
        $action->attachEventCallback('test_event', $callback1);
        $action->attachEventCallback('test_event', $callback2);
        $this->setExpectedException('\marvin255\bxfoundation\events\Exception', 'test_event');
        $action->attachEventCallback('test_event', $callback1);
    }

    public function testDetachEventCallback()
    {
        $action = new \marvin255\bxfoundation\routing\action\Component('component');

        $eventTrigger2 = 0;
        $callback2 = function () use (&$eventTrigger2) { ++$eventTrigger2; };
        $action->attachEventCallback('test_event', $callback2);

        $eventTrigger1 = 0;
        $callback1 = function () use (&$eventTrigger1) { ++$eventTrigger1; };
        $action->attachEventCallback('test_event', $callback1);
        $action->detachEventCallback('test_event', $callback1);

        $event = $this->getMockBuilder('\marvin255\bxfoundation\events\ResultInterface')
            ->getMock();
        $event->method('getName')->will($this->returnValue('test_event'));
        $event->method('isSuccess')->will($this->returnValue(true));
        $action->riseEvent($event);
        $action->riseEvent($event);
        $action->riseEvent($event);

        $this->assertSame(
            0,
            $eventTrigger1,
            'event handler must not fire if it was detached'
        );

        $this->assertSame(
            3,
            $eventTrigger2,
            'event handler must fire if it was not detached'
        );
    }

    public function testDetachEventCallbackEmptyNameException()
    {
        $action = new \marvin255\bxfoundation\routing\action\Component('component');

        $callback = function () {};
        $this->setExpectedException('\marvin255\bxfoundation\events\Exception');
        $action->detachEventCallback(null, $callback);
    }
}
