<?php

namespace marvin255\bxfoundation\tests\lib\routing\action;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorEmptyActionsException()
    {
        $this->setExpectedException('\marvin255\bxfoundation\routing\Exception');
        new \marvin255\bxfoundation\routing\action\Chain([]);
    }

    public function testConstructorWrongClassException()
    {
        $action1 = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();

        $action2 = $this->getMockBuilder('\marvin255\bxfoundation\routing\filter\FilterInterface')
            ->getMock();

        $this->setExpectedException('\marvin255\bxfoundation\routing\Exception', 'testKey');
        new \marvin255\bxfoundation\routing\action\Chain([
            $action1,
            'testKey' => $action2,
        ]);
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

        $content1 = (string) mt_rand();
        $action1 = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $action1->method('run')->will($this->returnValue($content1));

        $content2 = (string) mt_rand();
        $action2 = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $action2->method('run')->will($this->returnValue($content2));

        $chain = new \marvin255\bxfoundation\routing\action\Chain([$action1, $action2]);

        $this->assertSame(
            $content1 . $content2,
            $chain->run($ruleResult, $request, $response),
            'run method must concatenate data from all actions'
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

        $action = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $action->method('run')
            ->will($this->returnValue((string) mt_rand()));
        $chain = new \marvin255\bxfoundation\routing\action\Chain([$action]);
        $chain->attachEventCallback('onBeforeActionRun', function ($eventResult) use ($chain) {
            if ($chain === $eventResult->getTarget()) {
                $eventResult->fail();
            }
        });

        $this->assertSame(
            null,
            $chain->run($ruleResult, $request, $response),
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

        $action = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $action->method('run')
            ->will($this->returnValue((string) mt_rand()));
        $chain = new \marvin255\bxfoundation\routing\action\Chain([$action]);
        $chain->attachEventCallback('onAfterActionRun', function ($eventResult) use ($content) {
            $eventResult->setParam('return', $content);
        });

        $this->assertSame(
            $content,
            $chain->run($ruleResult, $request, $response),
            'run method must rises onAfterActionRun event'
        );
    }

    public function testAttachEventCallbackEmptyNameException()
    {
        $action = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $chain = new \marvin255\bxfoundation\routing\action\Chain([$action]);

        $this->setExpectedException('\marvin255\bxfoundation\events\Exception');
        $chain->attachEventCallback(null, function () {});
    }

    public function testAttachEventCallbackEmptyCallbackException()
    {
        $action = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $chain = new \marvin255\bxfoundation\routing\action\Chain([$action]);

        $this->setExpectedException('\marvin255\bxfoundation\events\Exception');
        $chain->attachEventCallback('test', 123);
    }

    public function testAttachEventCallbackDuplicateException()
    {
        $action = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $chain = new \marvin255\bxfoundation\routing\action\Chain([$action]);

        $callback1 = function () {};
        $callback2 = function () {};
        $chain->attachEventCallback('test_event', $callback1);
        $chain->attachEventCallback('test_event', $callback2);
        $this->setExpectedException('\marvin255\bxfoundation\events\Exception', 'test_event');
        $chain->attachEventCallback('test_event', $callback1);
    }

    public function testDetachEventCallback()
    {
        $action = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $chain = new \marvin255\bxfoundation\routing\action\Chain([$action]);

        $eventTrigger2 = 0;
        $callback2 = function () use (&$eventTrigger2) { ++$eventTrigger2; };
        $chain->attachEventCallback('test_event', $callback2);

        $eventTrigger1 = 0;
        $callback1 = function () use (&$eventTrigger1) { ++$eventTrigger1; };
        $chain->attachEventCallback('test_event', $callback1);
        $chain->detachEventCallback('test_event', $callback1);

        $event = $this->getMockBuilder('\marvin255\bxfoundation\events\ResultInterface')
            ->getMock();
        $event->method('getName')->will($this->returnValue('test_event'));
        $event->method('isSuccess')->will($this->returnValue(true));
        $chain->riseEvent($event);
        $chain->riseEvent($event);
        $chain->riseEvent($event);

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
        $action = $this->getMockBuilder('\marvin255\bxfoundation\routing\action\ActionInterface')
            ->getMock();
        $chain = new \marvin255\bxfoundation\routing\action\Chain([$action]);

        $callback = function () {};
        $this->setExpectedException('\marvin255\bxfoundation\events\Exception');
        $chain->detachEventCallback(null, $callback);
    }
}
