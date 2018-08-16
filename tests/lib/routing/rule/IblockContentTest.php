<?php

namespace marvin255\bxfoundation\tests\lib\routing\rule;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\services\iblock\Locator;
use marvin255\bxfoundation\request\Bitrix as Request;
use marvin255\bxfoundation\routing\rule\IblockContent;
use marvin255\bxfoundation\Exception;

class IblockContentTest extends BaseCase
{
    /**
     * @test
     */
    public function testConstructEmptyIblock()
    {
        $locator = $this->getMockBuilder(Locator::class)->getMock();

        $this->setExpectedException(Exception::class);
        $rule = new IblockContent($locator, false);
    }

    /**
     * @test
     */
    public function testUnexistedIblockException()
    {
        $iblockId = 'iblock_id_' . mt_rand();
        $locator = $this->getMockBuilder(Locator::class)->getMock();

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue('/test'));
        $rule = new IblockContent($locator, $iblockId);

        $this->setExpectedException(Exception::class, $iblockId);
        $rule->parse($request);
    }

    /**
     * @test
     */
    public function testParse()
    {
        $iblockId = mt_rand();
        $iblockType = 'iblock_type_' . mt_rand();
        $sections = [
            $this->createRandomSection(2, 1, 2),
            array_merge($this->createRandomSection(3, 10, 2), ['code' => 'code_2']),
            $this->createRandomSection(1, false),
        ];
        $element = $this->createRandomElement(10, 2);
        $awaitedParams = [
            'PATH' => [
                $sections[2],
                $sections[0],
                $element,
            ],
            'IBLOCK_ID' => $iblockId,
            'IBLOCK_TYPE_ID' => $iblockType,
        ];
        ksort($awaitedParams);

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue(
                "/{$sections[2]['code']}/{$sections[0]['code']}/{$element['code']}"
            ));

        $rule = $this->getMockBuilder(IblockContent::class)
            ->setConstructorArgs([$locator, $iblockId])
            ->setMethods(['loadSectionsByCodes', 'loadElementByCodeAndSection'])
            ->getMock();
        $rule->method('loadSectionsByCodes')
            ->with(
                $this->equalTo([$sections[2]['code'], $sections[0]['code'], $element['code']])
            )
            ->will($this->returnValue($sections));
        $rule->method('loadElementByCodeAndSection')
            ->with(
                $this->equalTo($element['code']),
                $this->equalTo($sections[0]['id'])
            )
            ->will($this->returnValue($element));

        $ruleResult = $rule->parse($request);
        $ruleParams = $ruleResult->getParams();
        ksort($ruleParams);

        $this->assertSame($awaitedParams, $ruleParams);
    }

    /**
     * @test
     */
    public function testParseNotFound()
    {
        $iblockId = mt_rand();
        $iblockType = 'iblock_type_' . mt_rand();
        $sections = [
            $this->createRandomSection(2, 1, 2),
            array_merge($this->createRandomSection(3, 10, 2), ['code' => 'code_2']),
            $this->createRandomSection(1, false),
        ];
        $element = $this->createRandomElement(10, 2);

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue(
                "/{$sections[0]['code']}/{$sections[2]['code']}/{$element['code']}"
            ));

        $rule = $this->getMockBuilder(IblockContent::class)
            ->setConstructorArgs([$locator, $iblockId])
            ->setMethods(['loadSectionsByCodes', 'loadElementByCodeAndSection'])
            ->getMock();
        $rule->method('loadSectionsByCodes')
            ->with(
                $this->equalTo([$sections[0]['code'], $sections[2]['code'], $element['code']])
            )
            ->will($this->returnValue($sections));
        $rule->method('loadElementByCodeAndSection')
            ->will($this->returnValue($element));

        $ruleResult = $rule->parse($request);

        $this->assertSame(null, $ruleResult);
    }

    /**
     * @test
     */
    public function testParseComplex()
    {
        $iblockId = mt_rand();
        $iblockType = 'iblock_type_' . mt_rand();
        $element = $this->createRandomElement(1, false, true);
        $awaitedParams = [
            'PATH' => [$element],
            'IBLOCK_ID' => $iblockId,
            'IBLOCK_TYPE_ID' => $iblockType,
        ];
        ksort($awaitedParams);

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue("/{$element['code']}/test/complex/"));

        $rule = $this->getMockBuilder(IblockContent::class)
            ->setConstructorArgs([$locator, $iblockId])
            ->setMethods(['loadSectionsByCodes', 'loadElementByCodeAndSection'])
            ->getMock();
        $rule->method('loadSectionsByCodes')
            ->with(
                $this->equalTo([$element['code'], 'test', 'complex'])
            )
            ->will($this->returnValue([]));
        $rule->method('loadElementByCodeAndSection')
            ->with(
                $this->equalTo($element['code']),
                $this->equalTo(false)
            )
            ->will($this->returnValue($element));

        $ruleResult = $rule->parse($request);
        $ruleParams = $ruleResult->getParams();
        ksort($ruleParams);

        $this->assertSame($awaitedParams, $ruleParams);
    }

    /**
     * @test
     */
    public function testParseComplexNotFound()
    {
        $iblockId = mt_rand();
        $iblockType = 'iblock_type_' . mt_rand();
        $element = $this->createRandomElement(1, false, false);

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')
            ->will($this->returnValue("/{$element['code']}/test/complex/"));

        $rule = $this->getMockBuilder(IblockContent::class)
            ->setConstructorArgs([$locator, $iblockId])
            ->setMethods(['loadSectionsByCodes', 'loadElementByCodeAndSection'])
            ->getMock();
        $rule->method('loadSectionsByCodes')
            ->with(
                $this->equalTo([$element['code'], 'test', 'complex'])
            )
            ->will($this->returnValue([]));
        $rule->method('loadElementByCodeAndSection')
            ->with(
                $this->equalTo($element['code']),
                $this->equalTo(false)
            )
            ->will($this->returnValue($element));

        $ruleResult = $rule->parse($request);

        $this->assertSame(null, $ruleResult);
    }

    /**
     * @test
     */
    public function testParseMain()
    {
        $iblockId = mt_rand();
        $iblockType = 'iblock_type_' . mt_rand();
        $element = [
            'id' => 1,
            'type' => 'element',
            'name' => '',
            'code' => '',
            'preview_text' => '',
            'preview_picture' => null,
            'detail_text' => '',
            'detail_picture' => null,
            'parent_id' => false,
            'is_complex' => false,
        ];
        $awaitedParams = [
            'PATH' => [$element],
            'IBLOCK_ID' => $iblockId,
            'IBLOCK_TYPE_ID' => $iblockType,
        ];
        ksort($awaitedParams);

        $locator = $this->getMockBuilder(Locator::class)
            ->getMock();
        $locator->method('findBy')
            ->with($this->equalTo('ID'), $this->equalTo($iblockId))
            ->will($this->returnValue([
                'ID' => $iblockId,
                'IBLOCK_TYPE_ID' => $iblockType,
            ]));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $request->method('getPath')->will($this->returnValue('/'));

        $rule = $this->getMockBuilder(IblockContent::class)
            ->setConstructorArgs([$locator, $iblockId])
            ->setMethods(['loadElementByCodeAndSection'])
            ->getMock();
        $rule->method('loadElementByCodeAndSection')
            ->with($this->equalTo(''), $this->equalTo(false))
            ->will($this->returnValue($element));

        $ruleResult = $rule->parse($request);
        $ruleParams = $ruleResult->getParams();
        ksort($ruleParams);

        $this->assertSame($awaitedParams, $ruleParams);
    }

    /**
     * Формирует случайный массив раздела по id раздела, id родителя
     * и глубине вложенности.
     *
     *  @param int $id
     *  @param bool|int $parentId
     *  @param int $depth
     *
     * @return array
     */
    protected function createRandomSection($id, $parentId, $depth = 1)
    {
        $return = [
            'id' => $id,
            'type' => 'section',
            'name' => "name_{$id}_" . mt_rand(),
            'code' => "code_section_{$id}",
            'preview_text' => "preview_text_{$id}_" . mt_rand(),
            'preview_picture' => [
                'SRC' => "src_picture_{$id}",
            ],
            'detail_text' => "detail_text_{$id}_" . mt_rand(),
            'detail_picture' => null,
            'parent_id' => $parentId,
            'depth' => $depth,
        ];
        ksort($return);

        return $return;
    }

    /**
     * Формирует случайный массив элемента по id раздела, id родителя
     * и глубине вложенности.
     *
     *  @param int $id
     *  @param int $parentId
     *  @param bool $isComplex
     *
     * @return array
     */
    protected function createRandomElement($id, $parentId, $isComplex = false)
    {
        $return = [
            'id' => $id,
            'type' => 'element',
            'name' => "name_{$id}_" . mt_rand(),
            'code' => "code_element_{$id}",
            'preview_text' => "preview_text_{$id}_" . mt_rand(),
            'preview_picture' => [
                'SRC' => "src_picture_{$id}",
            ],
            'detail_text' => "detail_text_{$id}_" . mt_rand(),
            'detail_picture' => null,
            'parent_id' => $parentId,
            'is_complex' => $isComplex,
        ];
        ksort($return);

        return $return;
    }
}
