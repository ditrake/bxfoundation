<?php

namespace marvin255\bxfoundation\tests\lib\services\cache;

use marvin255\bxfoundation\services\cache\Bitrix;
use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\services\Exception;

class BitrixTest extends BaseCase
{
    /**
     * @test
     */
    public function testSet()
    {
        $key = 'key_' . mt_rand();
        $vars = 'var_' . mt_rand();
        $tag1 = 'tag1_' . mt_rand();
        $tag2 = 'tag2_' . mt_rand();
        $time = mt_rand();

        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->setMethods(['initCache', 'startDataCache', 'endDataCache', 'clean'])
            ->getMock();
        $cache->expects($this->once())
            ->method('initCache')
            ->with($this->equalTo($time), $this->equalTo($key), $this->anything())
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('clean')
            ->with($this->equalTo($key), $this->anything());
        $cache->expects($this->once())
            ->method('startDataCache');
        $cache->expects($this->once())
            ->method('endDataCache')
            ->with($this->equalTo($vars));

        $taggedCache = $this->getMockBuilder('\Bitrix\Main\Data\TaggedCache')
            ->setMethods(['startTagCache', 'registerTag', 'endTagCache'])
            ->getMock();
        $taggedCache->expects($this->at(1))
            ->method('registerTag')
            ->with($this->equalTo($tag1));
        $taggedCache->expects($this->at(2))
            ->method('registerTag')
            ->with($this->equalTo($tag2));
        $taggedCache->expects($this->once())
            ->method('startTagCache');
        $taggedCache->expects($this->once())
            ->method('endTagCache');

        $bxCache = new Bitrix($cache, $taggedCache);

        $this->assertSame(
            $bxCache,
            $bxCache->set($key, $vars, $time, [$tag1, $tag2])
        );
    }

    /**
     * @test
     */
    public function testSetWithDefaultTime()
    {
        $key = 'key_' . mt_rand();
        $vars = 'var_' . mt_rand();
        $time = mt_rand();

        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->setMethods(['initCache', 'startDataCache', 'endDataCache'])
            ->getMock();
        $cache->expects($this->once())
            ->method('initCache')
            ->with($this->equalTo($time), $this->equalTo($key), $this->anything())
            ->will($this->returnValue(false));
        $cache->expects($this->once())
            ->method('startDataCache');
        $cache->expects($this->once())
            ->method('endDataCache')
            ->with($this->equalTo($vars));

        $bxCache = new Bitrix($cache, null, $time);
        $bxCache->set($key, $vars);
    }

    /**
     * @test
     */
    public function testSetEmptyKeyException()
    {
        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->getMock();

        $bxCache = new Bitrix($cache);

        $this->setExpectedException(Exception::class);
        $bxCache->set('', ['test']);
    }

    /**
     * @test
     */
    public function testGet()
    {
        $key = 'key_' . mt_rand();
        $vars = 'var_' . mt_rand();

        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->setMethods(['initCache', 'getVars'])
            ->getMock();
        $cache->expects($this->once())
            ->method('initCache')
            ->with($this->anything(), $this->equalTo($key), $this->anything())
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('getVars')
            ->will($this->returnValue($vars));

        $bxCache = new Bitrix($cache);

        $this->assertSame($vars, $bxCache->get($key));
    }

    /**
     * @test
     */
    public function testGetEmptyCache()
    {
        $key = 'key_' . mt_rand();

        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->setMethods(['initCache'])
            ->getMock();
        $cache->expects($this->once())
            ->method('initCache')
            ->with($this->anything(), $this->equalTo($key), $this->anything())
            ->will($this->returnValue(false));

        $bxCache = new Bitrix($cache);

        $this->assertSame(false, $bxCache->get($key));
    }

    /**
     * @test
     */
    public function testClear()
    {
        $key = 'key_' . mt_rand();

        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->setMethods(['initCache', 'cleanDir', 'clean'])
            ->getMock();
        $cache->expects($this->never())
            ->method('initCache');
        $cache->expects($this->once())
            ->method('clean')
            ->with($this->equalTo($key), $this->anything());

        $bxCache = new Bitrix($cache);

        $this->assertSame($bxCache, $bxCache->clear($key));
    }

    /**
     * @test
     */
    public function testClearByTag()
    {
        $tag = 'tag_' . mt_rand();

        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->getMock();
        $taggedCache = $this->getMockBuilder('\Bitrix\Main\Data\TaggedCache')
            ->setMethods(['clearByTag'])
            ->getMock();
        $taggedCache->expects($this->once())
            ->method('clearByTag')
            ->with($this->equalTo($tag));

        $bxCache = new Bitrix($cache, $taggedCache);

        $this->assertSame($bxCache, $bxCache->clearByTag($tag));
    }

    /**
     * @test
     */
    public function testClearEmptyKeyException()
    {
        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->getMock();

        $bxCache = new Bitrix($cache);

        $this->setExpectedException(Exception::class);
        $bxCache->clear('');
    }
}
