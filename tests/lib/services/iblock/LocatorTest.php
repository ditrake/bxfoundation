<?php

namespace marvin255\bxfoundation\tests\lib\services\iblock;

class LocatorTest extends \marvin255\bxfoundation\tests\BaseCase
{
    public function testConstructorWrongFilterException()
    {
        $this->setExpectedException('\marvin255\bxfoundation\services\Exception');
        new \marvin255\bxfoundation\services\iblock\Locator(123, []);
    }

    public function testConstructorWrongSelectException()
    {
        $this->setExpectedException('\marvin255\bxfoundation\services\Exception');
        new \marvin255\bxfoundation\services\iblock\Locator([], 123);
    }

    public function testGetList()
    {
        $testFilter = [
            'key_' . mt_rand() => 'value_' . mt_rand(),
            'key_1_' . mt_rand() => 'value_1_' . mt_rand(),
        ];
        $testSelect = [
            'test_param_' . mt_rand(),
            'test_param_1_' . mt_rand(),
        ];
        $list = [
            [
                'ID' => mt_rand(),
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
            [
                'ID' => mt_rand(),
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
        ];
        $locator = $this->getMock(
            '\marvin255\bxfoundation\services\iblock\Locator',
            ['loadList'],
            [$testFilter, $testSelect],
            '',
            true
        );
        $testSelect[] = 'ID';
        $locator->expects($this->once())
            ->method('loadList')
            ->with($this->equalTo($testFilter), $this->equalTo($testSelect))
            ->will($this->returnValue($list));

        $locator->getList();
        $locator->getList();
        $locator->getList();

        $this->assertSame($list, $locator->getList());
    }

    public function testGetListDefaults()
    {
        $list = [
            [
                'ID' => mt_rand(),
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
        ];
        $locator = $this->getMock(
            '\marvin255\bxfoundation\services\iblock\Locator',
            ['loadList'],
            [],
            '',
            true
        );
        $testSelect[] = 'ID';
        $locator->expects($this->once())
            ->method('loadList')
            ->with(
                $this->equalTo(['ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N']),
                $this->equalTo([
                    'ID',
                    'CODE',
                    'NAME',
                    'LID',
                    'IBLOCK_TYPE_ID',
                    'DETAIL_PAGE_URL',
                    'SECTION_PAGE_URL',
                    'LIST_PAGE_URL',
                    'PROPERTIES',
                ])
            )
            ->will($this->returnValue($list));

        $this->assertSame($list, $locator->getList());
    }

    public function testGetListDefaultsFromPublic()
    {
        $list = [
            [
                'ID' => mt_rand(),
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
        ];
        define('SITE_ID', 's1');
        $locator = $this->getMock(
            '\marvin255\bxfoundation\services\iblock\Locator',
            ['loadList'],
            [],
            '',
            true
        );
        $testSelect[] = 'ID';
        $locator->expects($this->once())
            ->method('loadList')
            ->with(
                $this->equalTo(['ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N', 'SITE_ID' => 's1']),
                $this->equalTo([
                    'ID',
                    'CODE',
                    'NAME',
                    'LID',
                    'IBLOCK_TYPE_ID',
                    'DETAIL_PAGE_URL',
                    'SECTION_PAGE_URL',
                    'LIST_PAGE_URL',
                    'PROPERTIES',
                ])
            )
            ->will($this->returnValue($list));

        $this->assertSame($list, $locator->getList());
    }

    public function testGetListCreateCache()
    {
        $testFilter = [
            'key_' . mt_rand() => 'value_' . mt_rand(),
            'key_1_' . mt_rand() => 'value_1_' . mt_rand(),
        ];
        $testSelect = [
            'test_param_' . mt_rand(),
            'test_param_1_' . mt_rand(),
        ];
        $list = [
            [
                'ID' => 1,
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
            [
                'ID' => 15,
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
        ];

        $cache = $this->getMockBuilder('\marvin255\bxfoundation\services\cache\CacheInterface')
            ->getMock();

        $locator = $this->getMock(
            '\marvin255\bxfoundation\services\iblock\Locator',
            ['loadList'],
            [$testFilter, $testSelect, $cache],
            '',
            true
        );
        $testSelect[] = 'ID';
        $locator->expects($this->once())
            ->method('loadList')
            ->with($this->equalTo($testFilter), $this->equalTo($testSelect))
            ->will($this->returnValue($list));

        $cid = get_class($locator) . json_encode(array_merge($testFilter, $testSelect));
        $cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($cid))
            ->will($this->returnValue(false));
        $cache->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($cid),
                $this->equalTo($list),
                $this->equalTo(null),
                $this->equalTo(['iblock_id_new', 'iblock_id_1', 'iblock_id_15'])
            );

        $this->assertSame($list, $locator->getList());
    }

    public function testGetListFromCache()
    {
        $testFilter = [
            'key_' . mt_rand() => 'value_' . mt_rand(),
            'key_1_' . mt_rand() => 'value_1_' . mt_rand(),
        ];
        $testSelect = [
            'test_param_' . mt_rand(),
            'test_param_1_' . mt_rand(),
        ];
        $list = [
            [
                'ID' => 1,
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
            [
                'ID' => 15,
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
        ];

        $cache = $this->getMockBuilder('\marvin255\bxfoundation\services\cache\CacheInterface')
            ->getMock();

        $locator = $this->getMock(
            '\marvin255\bxfoundation\services\iblock\Locator',
            ['loadList'],
            [$testFilter, $testSelect, $cache],
            '',
            true
        );
        $locator->expects($this->never())->method('loadList');

        $testSelect[] = 'ID';
        $cid = get_class($locator) . json_encode(array_merge($testFilter, $testSelect));
        $cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($cid))
            ->will($this->returnValue($list));

        $this->assertSame($list, $locator->getList());
    }

    public function testFindAllBy()
    {
        $list = [
            [
                'ID' => 1,
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
            [
                'ID' => 15,
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
        ];

        $locator = $this->getMock(
            '\marvin255\bxfoundation\services\iblock\Locator',
            ['loadList'],
            [],
            '',
            true
        );
        $locator->method('loadList')
            ->will($this->returnValue($list));

        $this->assertSame(
            [$list[1]],
            $locator->findAllBy('test_key_1', $list[1]['test_key_1'])
        );

        $this->assertSame(
            [$list[0]['test_key_2']],
            $locator->findAllBy('test_key_1', $list[0]['test_key_1'], 'test_key_2')
        );
    }

    public function testFindBy()
    {
        $list = [
            [
                'ID' => 1,
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
            [
                'ID' => 15,
                'test_key_1' => 'test_li_' . mt_rand(),
                'test_key_2' => 'test_li_1_' . mt_rand(),
            ],
        ];

        $locator = $this->getMock(
            '\marvin255\bxfoundation\services\iblock\Locator',
            ['loadList'],
            [],
            '',
            true
        );
        $locator->method('loadList')
            ->will($this->returnValue($list));

        $this->assertSame(
            $list[1],
            $locator->findBy('test_key_1', $list[1]['test_key_1'])
        );

        $this->assertSame(
            $list[0]['test_key_2'],
            $locator->findBy('test_key_1', $list[0]['test_key_1'], 'test_key_2')
        );
    }

    public function testGetCodeById()
    {
        $list = [
            [
                'ID' => mt_rand(),
                'CODE' => 'test_code_' . mt_rand(),
            ],
            [
                'ID' => mt_rand(),
                'CODE' => 'test_code_' . mt_rand(),
            ],
        ];

        $locator = $this->getMock(
            '\marvin255\bxfoundation\services\iblock\Locator',
            ['loadList'],
            [],
            '',
            true
        );
        $locator->method('loadList')
            ->will($this->returnValue($list));

        $this->assertSame(
            $list[1]['CODE'],
            $locator->getCodeById($list[1]['ID'])
        );

        $this->assertSame(
            null,
            $locator->getCodeById('wrong_id')
        );
    }

    public function testGetIdByCode()
    {
        $list = [
            [
                'ID' => mt_rand(),
                'CODE' => 'test_code_' . mt_rand(),
            ],
            [
                'ID' => mt_rand(),
                'CODE' => 'test_code_' . mt_rand(),
            ],
        ];

        $locator = $this->getMock(
            '\marvin255\bxfoundation\services\iblock\Locator',
            ['loadList'],
            [],
            '',
            true
        );
        $locator->method('loadList')
            ->will($this->returnValue($list));

        $this->assertSame(
            $list[1]['ID'],
            $locator->getIdByCode($list[1]['CODE'])
        );

        $this->assertSame(
            null,
            $locator->getIdByCode('wrong_code')
        );
    }
}
