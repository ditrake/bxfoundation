<?php

namespace marvin255\bxfoundation\tests\lib\routing\rule;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\services\iblock\Locator;
use marvin255\bxfoundation\request\Bitrix as Request;
use marvin255\bxfoundation\routing\rule\Iblock;
use marvin255\bxfoundation\routing\filter\FilterInterface;
use marvin255\bxfoundation\Exception;

class IblockTest extends BaseCase
{
    /**
     * @test
     */
    public function testConstructEmptyIblock()
    {
        $locator = $this->getMockBuilder(Locator::class)->getMock();

        $this->setExpectedException(Exception::class);
        $rule = new Iblock($locator, false);
    }

    /**
     * @test
     */
    public function testConstructWrongEntity()
    {
        $locator = $this->getMockBuilder(Locator::class)->getMock();

        $this->setExpectedException(Exception::class);
        $rule = new Iblock($locator, 'iblockId', 'wrong');
    }

    /**
     * @test
     */
    public function testParse()
    {
        $iblockId = mt_rand();
        $iblockType = mt_rand();
        $iblockElementId = mt_rand();
        $iblockSectionCode = 'section_code_' . mt_rand();
        $detailPageUrl = '/news/#SECTION_CODE#/#ID#';
        $awaitedParams = [
            'IBLOCK_ID' => $iblockId,
            'IBLOCK_TYPE_ID' => $iblockType,
            'ID' => $iblockElementId,
            'SECTION_CODE' => $iblockSectionCode,
        ];

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'DETAIL_PAGE_URL' => $detailPageUrl,
                'SECTION_PAGE_URL' => '/news/#SECTION_CODE#/',
                'LIST_PAGE_URL' => '/news/',
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/news/section_code/123'));

        $rule = $this->getMockBuilder(Iblock::class)
            ->setConstructorArgs([$locator, $iblockId, 'element'])
            ->setMethods(['processBitrixSef'])
            ->getMock();
        $rule->method('processBitrixSef')
            ->with(
                $this->equalTo($detailPageUrl),
                $this->equalTo($request)
            )
            ->will($this->returnValue([
                'SECTION_CODE' => $iblockSectionCode,
                'ID' => $iblockElementId,
            ]));

        $ruleResult = $rule->parse($request);
        $ruleParams = $ruleResult->getParams();
        ksort($ruleParams);

        $this->assertSame($awaitedParams, $ruleParams);
    }

    /**
     * @test
     */
    public function testParseIblockIdentityException()
    {
        $iblockId = mt_rand();

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')->will($this->returnValue(null));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $rule = new Iblock($locator, $iblockId);

        $this->setExpectedException(Exception::class, (string) $iblockId);
        $ruleResult = $rule->parse($request);
    }

    /**
     * @test
     */
    public function testCreateUrl()
    {
        $iblockId = mt_rand();
        $iblockType = mt_rand();
        $iblockElementId = mt_rand();
        $iblockSectionCode = 'section_code_' . mt_rand();
        $detailPageUrl = '/news/#SECTION_CODE#/#ID#';
        $awaitedUrl = "/news/{$iblockSectionCode}/{$iblockElementId}";

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'DETAIL_PAGE_URL' => $detailPageUrl,
                'SECTION_PAGE_URL' => '/news/#SECTION_CODE#/',
                'LIST_PAGE_URL' => '/news/',
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $rule = new Iblock($locator, $iblockId, 'element');
        $url = $rule->createUrl([
            'SECTION_CODE' => $iblockSectionCode,
            'ID' => $iblockElementId,
        ]);

        $this->assertSame($awaitedUrl, $url);
    }

    /**
     * @test
     */
    public function testCreateUrlIblockIdentityException()
    {
        $iblockId = mt_rand();

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')->will($this->returnValue(null));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $rule = new Iblock($locator, $iblockId);

        $this->setExpectedException(Exception::class, (string) $iblockId);
        $ruleResult = $rule->createUrl([]);
    }

    /**
     * @test
     */
    public function testCreateUrlRequiredParamException()
    {
        $iblockId = mt_rand();
        $iblockType = mt_rand();
        $iblockElementId = mt_rand();
        $iblockSectionCode = 'section_code_' . mt_rand();
        $detailPageUrl = '/news/#SECTION_CODE#/#ID#';
        $awaitedUrl = "/news/{$iblockSectionCode}/{$iblockElementId}";

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'DETAIL_PAGE_URL' => $detailPageUrl,
                'SECTION_PAGE_URL' => '/news/#SECTION_CODE#/',
                'LIST_PAGE_URL' => '/news/',
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $rule = new Iblock($locator, $iblockId, 'element');

        $this->setExpectedException(Exception::class, 'SECTION_CODE');
        $rule->createUrl(['ID' => $iblockElementId]);
    }

    /**
     * @test
     */
    public function testFilterParsing()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)
            ->setMethods([
                'onBeforeRouteParsing',
                'onAfterRouteParsing',
                'attachTo',
                'filter',
            ])->getMock();
        $filter->expects($this->once())->method('onBeforeRouteParsing');
        $filter->expects($this->once())->method('onAfterRouteParsing');
        $filter->method('attachTo')->will($this->returnCallback(function ($rule) use ($filter) {
            $rule->attachEventCallback('onBeforeRouteParsing', [$filter, 'onBeforeRouteParsing']);
            $rule->attachEventCallback('onAfterRouteParsing', [$filter, 'onAfterRouteParsing']);
        }));

        $iblockId = mt_rand();
        $iblockType = mt_rand();
        $iblockElementId = mt_rand();
        $iblockSectionCode = 'section_code_' . mt_rand();
        $detailPageUrl = '/news/#SECTION_CODE#/#ID#';

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'DETAIL_PAGE_URL' => $detailPageUrl,
                'SECTION_PAGE_URL' => '/news/#SECTION_CODE#/',
                'LIST_PAGE_URL' => '/news/',
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/news/section_code/123'));

        $rule = $this->getMockBuilder(Iblock::class)
            ->setConstructorArgs([$locator, $iblockId, 'element'])
            ->setMethods(['processBitrixSef'])
            ->getMock();
        $rule->method('processBitrixSef')
            ->with(
                $this->equalTo($detailPageUrl),
                $this->equalTo($request)
            )
            ->will($this->returnValue([
                'SECTION_CODE' => $iblockSectionCode,
                'ID' => $iblockElementId,
            ]));

        $rule->filter($filter)->parse($request);
    }

    /**
     * @test
     */
    public function testFilterCreatingUrl()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)
            ->setMethods([
                'onBeforeUrlCreating',
                'onAfterUrlCreating',
                'attachTo',
                'filter',
            ])->getMock();
        $filter->expects($this->once())->method('onBeforeUrlCreating');
        $filter->expects($this->once())->method('onAfterUrlCreating');
        $filter->method('attachTo')->will($this->returnCallback(function ($rule) use ($filter) {
            $rule->attachEventCallback('onBeforeUrlCreating', [$filter, 'onBeforeUrlCreating']);
            $rule->attachEventCallback('onAfterUrlCreating', [$filter, 'onAfterUrlCreating']);
        }));

        $iblockId = mt_rand();
        $iblockType = mt_rand();
        $iblockElementId = mt_rand();
        $iblockSectionCode = 'section_code_' . mt_rand();
        $detailPageUrl = '/news/#SECTION_CODE#/#ID#';
        $awaitedUrl = "/news/{$iblockSectionCode}/{$iblockElementId}";

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'DETAIL_PAGE_URL' => $detailPageUrl,
                'SECTION_PAGE_URL' => '/news/#SECTION_CODE#/',
                'LIST_PAGE_URL' => '/news/',
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $rule = new Iblock($locator, $iblockId, 'element');
        $rule->filter($filter)->createUrl([
            'SECTION_CODE' => $iblockSectionCode,
            'ID' => $iblockElementId,
        ]);
    }
}
