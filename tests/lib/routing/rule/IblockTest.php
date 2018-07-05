<?php

namespace marvin255\bxfoundation\tests\lib\routing\rule;

class IblockTest extends \marvin255\bxfoundation\tests\BaseCase
{
    public function testAttachFiltersInConstructor()
    {
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();

        $filter = $this->getMockBuilder('\marvin255\bxfoundation\routing\filter\Header')
            ->disableOriginalConstructor()
            ->setMethods(['attachTo'])
            ->getMock();
        $filter->expects($this->once())
            ->method('attachTo');

        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', 'element', [$filter]);
    }

    public function testConstructEmptyIblockEntity()
    {
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();
        $this->setExpectedException('\marvin255\bxfoundation\routing\Exception');
        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, false);
    }

    public function testConstructWrongIblockEntitites()
    {
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();
        $this->setExpectedException('\marvin255\bxfoundation\routing\Exception', 'diff1, diff2');
        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', ['element', 'diff1', 'diff2']);
    }

    public function testParseDetail()
    {
        $iblockId = mt_rand();
        $iblockType = mt_rand();

        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
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

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/news/section_code/123'));

        $rule = $this->getMock(
            '\marvin255\bxfoundation\routing\rule\Iblock',
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

        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
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

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/news/section_code'));

        $rule = $this->getMock(
            '\marvin255\bxfoundation\routing\rule\Iblock',
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

        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
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

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/news/'));

        $rule = $this->getMock(
            '\marvin255\bxfoundation\routing\rule\Iblock',
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

        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue(null));

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();

        $rule = $this->getMock(
            '\marvin255\bxfoundation\routing\rule\Iblock',
            ['processBitrixSef'],
            [$locator, $iblockId, ['element', 'iblock', 'section']],
            '',
            true
        );

        $this->setExpectedException(
            '\marvin255\bxfoundation\routing\Exception',
            (string) $iblockId
        );
        $ruleResult = $rule->parse($request);
    }

    public function testAttachFilters()
    {
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();

        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', ['element']);

        $filter = $this->getMockBuilder('\marvin255\bxfoundation\routing\filter\Header')
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
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();

        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', ['element']);

        $filter = $this->getMockBuilder('\marvin255\bxfoundation\routing\rule\Regexp')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectedException(
            '\marvin255\bxfoundation\routing\Exception',
            'testKey'
        );
        $rule->attachFilters(['testKey' => $filter]);
    }

    public function testOnBeforeRouteParsing()
    {
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();

        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('test'));

        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', ['element']);

        $filter = $this->getMockBuilder('\marvin255\bxfoundation\routing\filter\Header')
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
        $request = $this->getMockBuilder('\marvin255\bxfoundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/news/section'));

        $iblockId = mt_rand();
        $iblockType = mt_rand();

        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
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
            '\marvin255\bxfoundation\routing\rule\Iblock',
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

        $filter = $this->getMockBuilder('\marvin255\bxfoundation\routing\filter\Header')
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

        $this->setExpectedException('\marvin255\bxfoundation\routing\ForbiddenException');
        $rule->parse($request);
    }

    public function testAttachEventCallbackEmptyNameException()
    {
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();
        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', ['element']);
        $this->setExpectedException('\marvin255\bxfoundation\events\Exception');
        $rule->attachEventCallback(null, function () {});
    }

    public function testAttachEventCallbackEmptyCallbackException()
    {
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();
        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', ['element']);
        $this->setExpectedException('\marvin255\bxfoundation\events\Exception');
        $rule->attachEventCallback('test', 123);
    }

    public function testAttachEventCallbackDuplicateException()
    {
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();
        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', ['element']);
        $callback1 = function () {};
        $callback2 = function () {};
        $rule->attachEventCallback('test_event', $callback1);
        $rule->attachEventCallback('test_event', $callback2);
        $this->setExpectedException('\marvin255\bxfoundation\events\Exception', 'test_event');
        $rule->attachEventCallback('test_event', $callback1);
    }

    public function testDetachEventCallback()
    {
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();

        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', ['element']);

        $eventTrigger2 = 0;
        $callback2 = function () use (&$eventTrigger2) { ++$eventTrigger2; };
        $rule->attachEventCallback('test_event', $callback2);

        $eventTrigger1 = 0;
        $callback1 = function () use (&$eventTrigger1) { ++$eventTrigger1; };
        $rule->attachEventCallback('test_event', $callback1);
        $rule->detachEventCallback('test_event', $callback1);

        $event = $this->getMockBuilder('\marvin255\bxfoundation\events\ResultInterface')
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
        $locator = $this->getMockBuilder('\marvin255\bxfoundation\services\iblock\Locator')
            ->getMock();
        $rule = new \marvin255\bxfoundation\routing\rule\Iblock($locator, 'test', ['element']);
        $callback = function () {};
        $this->setExpectedException('\marvin255\bxfoundation\events\Exception');
        $rule->detachEventCallback(null, $callback);
    }
}
