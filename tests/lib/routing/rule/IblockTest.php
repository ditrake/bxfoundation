<?php

namespace creative\foundation\tests\lib\routing\rule;

class IblockTest extends \PHPUnit_Framework_TestCase
{
    public function testAttachFiltersInConstructor()
    {
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();

        $filter = $this->getMockBuilder('\creative\foundation\routing\filter\Header')
            ->disableOriginalConstructor()
            ->setMethods(['attachTo'])
            ->getMock();
        $filter->expects($this->once())
            ->method('attachTo');

        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', 'element', [$filter]);
    }

    public function testConstructEmptyIblockEntity()
    {
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $this->setExpectedException('\creative\foundation\routing\Exception');
        $rule = new \creative\foundation\routing\rule\Iblock($locator, false);
    }

    public function testConstructWrongIblockEntitites()
    {
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $this->setExpectedException('\creative\foundation\routing\Exception', 'diff1, diff2');
        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', ['element', 'diff1', 'diff2']);
    }

    public function testParseDetail()
    {
        $iblockId = mt_rand();
        $iblockType = mt_rand();

        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'DETAIL_PAGE_URL' => '/news/#SECTION_CODE#/#ID#',
                'SECTION_PAGE_URL' => '/news/#SECTION_CODE#/',
                'LIST_PAGE_URL' => '/news/',
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/news/section_code/123'));

        $rule = $this->getMock(
            '\creative\foundation\routing\rule\Iblock',
            ['processBitrixSef'],
            [$locator, $iblockId, ['element', 'iblock', 'section']],
            '',
            true
        );
        $rule->method('processBitrixSef')
            ->with(
                $this->equalTo('/news/#SECTION_CODE#/#ID#'),
                $this->equalTo($request)
            )
            ->will($this->returnValue([
                'SECTION_CODE' => 'section_code',
                'ID' => '123',
            ]));

        $ruleResult = $rule->parse($request);
        $this->assertSame(
            [
                'SECTION_CODE' => 'section_code',
                'ID' => '123',
                'IBLOCK_ID' => (int) $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ],
            $ruleResult->getParams(),
            'Iblock rule must parse detail rule by iblock settings'
        );
    }

    public function testParseSection()
    {
        $iblockId = mt_rand();
        $iblockType = mt_rand();

        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'DETAIL_PAGE_URL' => '/news/#SECTION_CODE#/#ID#',
                'SECTION_PAGE_URL' => '/news/#SECTION_CODE#/',
                'LIST_PAGE_URL' => '/news/',
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/news/section_code'));

        $rule = $this->getMock(
            '\creative\foundation\routing\rule\Iblock',
            ['processBitrixSef'],
            [$locator, $iblockId, ['element', 'iblock', 'section']],
            '',
            true
        );
        $rule->method('processBitrixSef')
            ->with(
                $this->equalTo('/news/#SECTION_CODE#/'),
                $this->equalTo($request)
            )
            ->will($this->returnValue([
                'SECTION_CODE' => 'section_code',
            ]));

        $ruleResult = $rule->parse($request);
        $this->assertSame(
            [
                'SECTION_CODE' => 'section_code',
                'IBLOCK_ID' => (int) $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ],
            $ruleResult->getParams(),
            'Iblock rule must parse section rule by iblock settings'
        );
    }

    public function testParseIblock()
    {
        $iblockId = mt_rand();
        $iblockType = mt_rand();

        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'DETAIL_PAGE_URL' => '/news/#SECTION_CODE#/#ID#',
                'SECTION_PAGE_URL' => '/news/#SECTION_CODE#/',
                'LIST_PAGE_URL' => '/news/',
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/news/'));

        $rule = $this->getMock(
            '\creative\foundation\routing\rule\Iblock',
            ['processBitrixSef'],
            [$locator, $iblockId, ['element', 'iblock', 'section']],
            '',
            true
        );
        $rule->method('processBitrixSef')
            ->with(
                $this->equalTo('/news/'),
                $this->equalTo($request)
            )
            ->will($this->returnValue([]));

        $ruleResult = $rule->parse($request);
        $this->assertSame(
            [
                'IBLOCK_ID' => (int) $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ],
            $ruleResult->getParams(),
            'Iblock rule must parse iblock rule by iblock settings'
        );
    }

    public function testParseIblockIdentityException()
    {
        $iblockId = mt_rand();
        $iblockType = mt_rand();

        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue(null));

        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();

        $rule = $this->getMock(
            '\creative\foundation\routing\rule\Iblock',
            ['processBitrixSef'],
            [$locator, $iblockId, ['element', 'iblock', 'section']],
            '',
            true
        );

        $this->setExpectedException('\creative\foundation\routing\Exception', $iblockId);
        $ruleResult = $rule->parse($request);
    }

    public function testAttachFilters()
    {
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();

        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', ['element']);

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
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();

        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', ['element']);

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
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();

        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('test'));

        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', ['element']);

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
            ->will($this->returnValue('/news/section'));

        $iblockId = mt_rand();
        $iblockType = mt_rand();

        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'DETAIL_PAGE_URL' => '/news/#SECTION_CODE#/#ID#',
                'SECTION_PAGE_URL' => '/news/#SECTION_CODE#/',
                'LIST_PAGE_URL' => '/news/',
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $rule = $this->getMock(
            '\creative\foundation\routing\rule\Iblock',
            ['processBitrixSef'],
            [$locator, $iblockId, ['element', 'iblock', 'section']],
            '',
            true
        );
        $rule->method('processBitrixSef')
            ->with(
                $this->equalTo('/news/#SECTION_CODE#/'),
                $this->equalTo($request)
            )
            ->will($this->returnValue([
                'SECTION_CODE' => 'section_code',
            ]));

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
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', ['element']);
        $this->setExpectedException('\creative\foundation\events\Exception');
        $rule->attachEventCallback(null, function () {});
    }

    public function testAttachEventCallbackEmptyCallbackException()
    {
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', ['element']);
        $this->setExpectedException('\creative\foundation\events\Exception');
        $rule->attachEventCallback('test', 123);
    }

    public function testAttachEventCallbackDuplicateException()
    {
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', ['element']);
        $callback1 = function () {};
        $callback2 = function () {};
        $rule->attachEventCallback('test_event', $callback1);
        $rule->attachEventCallback('test_event', $callback2);
        $this->setExpectedException('\creative\foundation\events\Exception', 'test_event');
        $rule->attachEventCallback('test_event', $callback1);
    }

    public function testDetachEventCallback()
    {
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();

        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', ['element']);

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
        $locator = $this->getMockBuilder('\creative\foundation\services\iblock\Locator')
            ->getMock();
        $rule = new \creative\foundation\routing\rule\Iblock($locator, 'test', ['element']);
        $callback = function () {};
        $this->setExpectedException('\creative\foundation\events\Exception');
        $rule->detachEventCallback(null, $callback);
    }
}
