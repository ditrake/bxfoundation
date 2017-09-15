<?php

namespace marvin255\bxfoundation\tests\lib\application;

class ServiceLocatortTest extends \PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $serviceName1 = 'test' . mt_rand();
        $service1 = $this->getMockBuilder('\StdClass')->getMock();
        $serviceName2 = 'test' . mt_rand();
        $service2 = $this->getMockBuilder('\StdClass')->getMock();
        $locator = new \marvin255\bxfoundation\application\ServiceLocator;

        $this->assertSame(
            $locator,
            $locator->set($serviceName1, $service1),
            'setStatus method must returns it\'s object'
        );

        $this->assertSame(
            $service1,
            $locator->get($serviceName1),
            'get method must gets service setted by set'
        );

        $locator->$serviceName2 = $service2;
        $this->assertSame(
            $service2,
            $locator->$serviceName2,
            'magic must use set and get methods'
        );
    }

    public function testSetWithNonObjectException()
    {
        $serviceName = 'test' . mt_rand();
        $locator = new \marvin255\bxfoundation\application\ServiceLocator;
        $this->setExpectedException('\marvin255\bxfoundation\application\Exception', $serviceName);
        $locator->set($serviceName, '123');
    }

    public function testSetWithEmptyNameException()
    {
        $service = $this->getMockBuilder('\StdClass')->getMock();
        $locator = new \marvin255\bxfoundation\application\ServiceLocator;
        $this->setExpectedException('\marvin255\bxfoundation\application\Exception');
        $locator->set(false, $service);
    }

    public function testGetWithUnaviableServiceException()
    {
        $serviceName = 'test' . mt_rand();
        $locator = new \marvin255\bxfoundation\application\ServiceLocator;
        $this->setExpectedException('\marvin255\bxfoundation\application\Exception', $serviceName);
        $locator->get($serviceName);
    }

    public function testHas()
    {
        $serviceName = 'test' . mt_rand();
        $service = $this->getMockBuilder('\StdClass')->getMock();
        $locator = new \marvin255\bxfoundation\application\ServiceLocator;
        $locator->set($serviceName, $service);

        $this->assertSame(
            true,
            $locator->has($serviceName),
            'has method must checks if service exists'
        );

        $this->assertSame(
            false,
            $locator->has('test'),
            'has method must checks if service doesn\'t exists'
        );
    }

    public function testDelete()
    {
        $serviceName1 = 'test' . mt_rand();
        $service1 = $this->getMockBuilder('\StdClass')->getMock();
        $serviceName2 = 'test' . mt_rand();
        $service2 = $this->getMockBuilder('\StdClass')->getMock();
        $locator = new \marvin255\bxfoundation\application\ServiceLocator;

        $locator->set($serviceName1, $service1);
        $this->assertSame(
            $locator,
            $locator->delete($serviceName1),
            'unset method must returns it\'s object'
        );
        $this->assertSame(
            false,
            $locator->has($serviceName1),
            'unset must unset the servise'
        );

        $locator->$serviceName2 = $service2;
        unset($locator->$serviceName2);
        $this->assertSame(
            false,
            isset($locator->$serviceName2),
            'unset must unset the servise'
        );
    }

    public function testDeleteUnaviableServiceException()
    {
        $serviceName = 'test' . mt_rand();
        $locator = new \marvin255\bxfoundation\application\ServiceLocator;
        $this->setExpectedException('\marvin255\bxfoundation\application\Exception', $serviceName);
        $locator->delete($serviceName);
    }
}
