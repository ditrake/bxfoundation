<?php

namespace marvin255\bxfoundation\tests\lib\application;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\application\ServiceLocator;
use marvin255\bxfoundation\application\Exception;
use stdClass;

class ServiceLocatortTest extends BaseCase
{
    /**
     * @test
     */
    public function testSet()
    {
        $serviceName1 = 'test' . mt_rand();
        $service1 = $this->getMockBuilder(stdClass::class)->getMock();
        $serviceName2 = 'test' . mt_rand();
        $service2 = $this->getMockBuilder(stdClass::class)->getMock();

        $locator = new ServiceLocator;
        $locator->$serviceName2 = $service2;

        $this->assertSame($locator, $locator->set($serviceName1, $service1));
        $this->assertSame($service1, $locator->get($serviceName1));
        $this->assertSame($service2, $locator->get($serviceName2));
    }

    /**
     * @test
     */
    public function testSetWithNonObjectException()
    {
        $serviceName = 'test' . mt_rand();

        $locator = new ServiceLocator;

        $this->setExpectedException(Exception::class, $serviceName);
        $locator->set($serviceName, '123');
    }

    /**
     * @test
     */
    public function testSetWithEmptyNameException()
    {
        $service = $this->getMockBuilder(stdClass::class)->getMock();

        $locator = new ServiceLocator;

        $this->setExpectedException(Exception::class);
        $locator->set(false, $service);
    }

    /**
     * @test
     */
    public function testGetWithUnaviableServiceException()
    {
        $serviceName = 'test' . mt_rand();

        $locator = new ServiceLocator;

        $this->setExpectedException(Exception::class, $serviceName);
        $locator->get($serviceName);
    }

    /**
     * @test
     */
    public function testHas()
    {
        $serviceName = 'test' . mt_rand();
        $service = $this->getMockBuilder(stdClass::class)->getMock();

        $locator = new ServiceLocator;
        $locator->set($serviceName, $service);

        $this->assertSame(true, $locator->has($serviceName));
        $this->assertSame(true, isset($locator->$serviceName));
        $this->assertSame(false, $locator->has('test'));
    }

    /**
     * @test
     */
    public function testDelete()
    {
        $serviceName1 = 'test' . mt_rand();
        $service1 = $this->getMockBuilder(stdClass::class)->getMock();
        $serviceName2 = 'test' . mt_rand();
        $service2 = $this->getMockBuilder(stdClass::class)->getMock();

        $locator = new ServiceLocator;
        $locator->set($serviceName1, $service1);
        $locator->$serviceName2 = $service2;
        unset($locator->$serviceName2);

        $this->assertSame($locator, $locator->delete($serviceName1));
        $this->assertSame(false, $locator->has($serviceName1));
        $this->assertSame(false, $locator->has($serviceName2));
    }

    /**
     * @test
     */
    public function testDeleteUnaviableServiceException()
    {
        $serviceName = 'test' . mt_rand();

        $locator = new ServiceLocator;

        $this->setExpectedException(Exception::class, $serviceName);
        $locator->delete($serviceName);
    }
}
