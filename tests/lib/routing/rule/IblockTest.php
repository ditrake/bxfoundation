<?php

namespace marvin255\bxfoundation\tests\lib\routing\rule;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\services\iblock\Locator;
use marvin255\bxfoundation\request\Bitrix as Request;
use marvin255\bxfoundation\routing\rule\Iblock;
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
        $iblockEmentId = mt_rand();
        $iblockSectionCode = 'section_code_' . mt_rand();
        $detailPageUrl = '/news/#SECTION_CODE#/#ID#';
        $awaitedParams = [
            'IBLOCK_ID' => $iblockId,
            'IBLOCK_TYPE_ID' => $iblockType,
            'ID' => $iblockEmentId,
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
                'ID' => $iblockEmentId,
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
}
