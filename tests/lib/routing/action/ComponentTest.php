<?php

namespace creative\foundation\tests\lib\routing\action;

class ComponentTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorEmptyComponentException()
    {
        $this->setExpectedException('\creative\foundation\routing\Exception');
        new \creative\foundation\routing\action\Component('');
    }

    public function testRun()
    {
        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder('\creative\foundation\response\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();

        $testParam = mt_rand();
        $ruleResult = $this->getMockBuilder('\creative\foundation\routing\rule\RuleResultInterface')
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

        $action = new \creative\foundation\routing\action\Component(
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
        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder('\creative\foundation\response\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $ruleResult = $this->getMockBuilder('\creative\foundation\routing\rule\RuleResultInterface')
            ->getMock();

        $action = new \creative\foundation\routing\action\Component('component');
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

        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder('\creative\foundation\response\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $ruleResult = $this->getMockBuilder('\creative\foundation\routing\rule\RuleResultInterface')
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

        $action = new \creative\foundation\routing\action\Component(
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
        $action = new \creative\foundation\routing\action\Component('component');

        $this->setExpectedException('\creative\foundation\events\Exception');
        $action->attachEventCallback(null, function () {});
    }

    public function testAttachEventCallbackEmptyCallbackException()
    {
        $action = new \creative\foundation\routing\action\Component('component');

        $this->setExpectedException('\creative\foundation\events\Exception');
        $action->attachEventCallback('test', 123);
    }

    public function testAttachEventCallbackDuplicateException()
    {
        $action = new \creative\foundation\routing\action\Component('component');

        $callback1 = function () {};
        $callback2 = function () {};
        $action->attachEventCallback('test_event', $callback1);
        $action->attachEventCallback('test_event', $callback2);
        $this->setExpectedException('\creative\foundation\events\Exception', 'test_event');
        $action->attachEventCallback('test_event', $callback1);
    }

    public function testDetachEventCallback()
    {
        $action = new \creative\foundation\routing\action\Component('component');

        $eventTrigger2 = 0;
        $callback2 = function () use (&$eventTrigger2) { ++$eventTrigger2; };
        $action->attachEventCallback('test_event', $callback2);

        $eventTrigger1 = 0;
        $callback1 = function () use (&$eventTrigger1) { ++$eventTrigger1; };
        $action->attachEventCallback('test_event', $callback1);
        $action->detachEventCallback('test_event', $callback1);

        $event = $this->getMockBuilder('\creative\foundation\events\ResultInterface')
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
        $action = new \creative\foundation\routing\action\Component('component');

        $callback = function () {};
        $this->setExpectedException('\creative\foundation\events\Exception');
        $action->detachEventCallback(null, $callback);
    }
}
