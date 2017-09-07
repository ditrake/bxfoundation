<?php

namespace creative\foundation\tests\lib\routing\rule;

class RegexpTest extends \PHPUnit_Framework_TestCase
{
    public function testAttachFiltersInConstructor()
    {
        $filter = $this->getMockBuilder('\creative\foundation\routing\filter\Header')
            ->disableOriginalConstructor()
            ->setMethods(['attachTo'])
            ->getMock();

        $filter->expects($this->once())
            ->method('attachTo');

        $rule = new \creative\foundation\routing\rule\Regexp('test', [$filter]);
    }

    public function testEmptyRegexpConstructorException()
    {
        $this->setExpectedException('\creative\foundation\routing\Exception');
        $rule = new \creative\foundation\routing\rule\Regexp(null);
    }

    public function testParse()
    {
        $id = (string) mt_rand();
        $code = 'qwe';
        $truePath = "/test/{$id}/{$code}/";
        $requestTrue = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $requestTrue->method('getPath')
            ->will($this->returnValue($truePath));

        $falsePath = '/test1/' . mt_rand() . '/qwe/' . mt_rand();
        $requestFalse = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $requestFalse->method('getPath')
            ->will($this->returnValue($falsePath));

        $rule = new \creative\foundation\routing\rule\Regexp('/test/<id:\d+>/<code:[a-z]+>');

        $this->assertSame(
            ['id' => $id, 'code' => $code],
            $rule->parse($requestTrue)->getParams(),
            'parse method must checks url and returns parsed data'
        );

        $this->assertSame(
            null,
            $rule->parse($requestFalse),
            'parse method must checks url and returns null if it is wrong'
        );
    }

    public function testParseException()
    {
        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $rule = new \creative\foundation\routing\rule\Regexp('/test/ ewer/<code:[a-z]+>');
        $this->setExpectedException(
            '\creative\foundation\routing\Exception',
            ' ewer'
        );
        $rule->parse($request);
    }

    public function testAttachFilters()
    {
        $rule = new \creative\foundation\routing\rule\Regexp('test');

        $filter = $this->getMockBuilder('\creative\foundation\routing\filter\Header')
            ->disableOriginalConstructor()
            ->setMethods(['attachTo'])
            ->getMock();
        $filter->expects($this->once())
            ->method('attachTo')
            ->with($this->equalTo($rule));

        $rule->attachFilters([$filter]);
    }

    public function testAttachFiltersWrongClassException()
    {
        $rule = new \creative\foundation\routing\rule\Regexp('test');

        $filter = $this->getMockBuilder('\creative\foundation\routing\rule\Regexp')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectedException(
            '\creative\foundation\routing\Exception',
            'testKey'
        );
        $rule->attachFilters(['testKey' => $filter]);
    }

    public function testOnBeforeRouteParsing()
    {
        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('test'));

        $rule = new \creative\foundation\routing\rule\Regexp('test');

        $filter = $this->getMockBuilder('\creative\foundation\routing\filter\Header')
            ->disableOriginalConstructor()
            ->setMethods(['attachTo'])
            ->getMock();
        $filter->method('attachTo')
            ->will($this->returnCallback(function ($target) use ($filter, $rule) {
                $target->attachEventCallback('onBeforeRouteParsing', function ($eventResult) use ($filter, $rule) {
                    if ($rule === $eventResult->getTarget()) {
                        $eventResult->fail();
                    }
                });
            }));

        $rule->attachFilters([$filter]);

        $this->assertSame(
            null,
            $rule->parse($request),
            'parse method must rises onBeforeRouteParsing event'
        );
    }

    public function testOnAfterRouteParsing()
    {
        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('test'));

        $rule = new \creative\foundation\routing\rule\Regexp('test');

        $filter = $this->getMockBuilder('\creative\foundation\routing\filter\Header')
            ->disableOriginalConstructor()
            ->setMethods(['attachTo'])
            ->getMock();
        $filter->method('attachTo')
            ->will($this->returnCallback(function ($target) {
                $target->attachEventCallback('onAfterRouteParsing', function ($eventResult) {
                    $eventResult->fail();
                });
            }));

        $rule->attachFilters([$filter]);

        $this->setExpectedException('\creative\foundation\routing\ForbiddenException');
        $rule->parse($request);
    }

    public function testAttachEventCallbackEmptyNameException()
    {
        $rule = new \creative\foundation\routing\rule\Regexp('test');
        $this->setExpectedException('\creative\foundation\events\Exception');
        $rule->attachEventCallback(null, function () {});
    }

    public function testAttachEventCallbackEmptyCallbackException()
    {
        $rule = new \creative\foundation\routing\rule\Regexp('test');
        $this->setExpectedException('\creative\foundation\events\Exception');
        $rule->attachEventCallback('test', 123);
    }

    public function testAttachEventCallbackDuplicateException()
    {
        $rule = new \creative\foundation\routing\rule\Regexp('test');
        $callback1 = function () {};
        $callback2 = function () {};
        $rule->attachEventCallback('test_event', $callback1);
        $rule->attachEventCallback('test_event', $callback2);
        $this->setExpectedException('\creative\foundation\events\Exception', 'test_event');
        $rule->attachEventCallback('test_event', $callback1);
    }

    public function testDetachEventCallback()
    {
        $rule = new \creative\foundation\routing\rule\Regexp('test');

        $eventTrigger2 = 0;
        $callback2 = function () use (&$eventTrigger2) { ++$eventTrigger2; };
        $rule->attachEventCallback('test_event', $callback2);

        $eventTrigger1 = 0;
        $callback1 = function () use (&$eventTrigger1) { ++$eventTrigger1; };
        $rule->attachEventCallback('test_event', $callback1);
        $rule->detachEventCallback('test_event', $callback1);

        $event = $this->getMockBuilder('\creative\foundation\events\ResultInterface')
            ->getMock();
        $event->method('getName')->will($this->returnValue('test_event'));
        $event->method('isSuccess')->will($this->returnValue(true));
        $rule->riseEvent($event);
        $rule->riseEvent($event);
        $rule->riseEvent($event);

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
        $rule = new \creative\foundation\routing\rule\Regexp('test');
        $callback = function () {};
        $this->setExpectedException('\creative\foundation\events\Exception');
        $rule->detachEventCallback(null, $callback);
    }
}
