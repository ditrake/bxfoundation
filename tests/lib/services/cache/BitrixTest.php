<?php

namespace marvin255\bxfoundation\tests\lib\services\cache;

class BitrixTest extends \marvin255\bxfoundation\tests\BaseCase
{
    public function testSet()
    {
        $key = 'key_' . mt_rand();
        $vars = 'var_' . mt_rand();
        $tag1 = 'tag1_' . mt_rand();
        $tag2 = 'tag2_' . mt_rand();
        $time = mt_rand();

        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->setMethods(['initCache', 'startDataCache', 'endDataCache'])
            ->getMock();
        $cache->expects($this->once())
            ->method('initCache')
            ->with($this->equalTo($time), $this->equalTo($key), $this->anything())
            ->will($this->returnValue(true));
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

        $bxCache = new \marvin255\bxfoundation\services\cache\Bitrix($cache, $taggedCache);

        $this->assertSame(
            $bxCache,
            $bxCache->set($key, $vars, $time, [$tag1, $tag2]),
            'set method must returns it\'s object'
        );
    }

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
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('startDataCache');
        $cache->expects($this->once())
            ->method('endDataCache')
            ->with($this->equalTo($vars));

        $bxCache = new \marvin255\bxfoundation\services\cache\Bitrix($cache, null, $time);
        $bxCache->set($key, $vars);
    }

    public function testSetEmptyKeyException()
    {
        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->getMock();

        $bxCache = new \marvin255\bxfoundation\services\cache\Bitrix($cache);

        $this->setExpectedException('\marvin255\bxfoundation\services\Exception');
        $bxCache->set('', ['test']);
    }

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

        $bxCache = new \marvin255\bxfoundation\services\cache\Bitrix($cache);

        $this->assertSame(
            $vars,
            $bxCache->get($key),
            'get method must calls bitrix cache'
        );
    }

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

        $bxCache = new \marvin255\bxfoundation\services\cache\Bitrix($cache);

        $this->assertSame(
            false,
            $bxCache->get($key),
            'get method must calls bitrix cache'
        );
    }

    public function testClear()
    {
        $key = 'key_' . mt_rand();

        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->setMethods(['initCache', 'cleanDir'])
            ->getMock();
        $cache->expects($this->once())
            ->method('initCache')
            ->with($this->anything(), $this->equalTo($key), $this->anything())
            ->will($this->returnValue(false));
        $cache->expects($this->once())
            ->method('cleanDir')
            ->with($this->anything());

        $bxCache = new \marvin255\bxfoundation\services\cache\Bitrix($cache);

        $this->assertSame(
            $bxCache,
            $bxCache->clear($key),
            'clear method must returns it\'s object'
        );
    }

    public function testClearEmptyKeyException()
    {
        $cache = $this->getMockBuilder('\Bitrix\Main\Data\Cache')
            ->getMock();

        $bxCache = new \marvin255\bxfoundation\services\cache\Bitrix($cache);

        $this->setExpectedException('\marvin255\bxfoundation\services\Exception');
        $bxCache->clear('');
    }
}
