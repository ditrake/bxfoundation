<?php

namespace marvin255\bxfoundation\tests\lib\events;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\events\Result;
use marvin255\bxfoundation\events\Exception;
use StdClass;

class ResultTest extends BaseCase
{
    /**
     * @test
     */
    public function testGetName()
    {
        $result = new Result('name', new stdClass);

        $this->assertSame('name', $result->getName());
    }

    /**
     * @test
     */
    public function testFail()
    {
        $result = new Result('name', new stdClass);

        $this->assertSame(true, $result->isSuccess());
        $this->assertSame($result, $result->fail());
        $this->assertSame(false, $result->isSuccess());
    }

    /**
     * @test
     */
    public function testSetParams()
    {
        $defaultParams = [
            'default1' => mt_rand(),
            'default2' => mt_rand(),
            'default3' => mt_rand(),
        ];
        $newParams = [
            'new1' => mt_rand(),
            'new2' => mt_rand(),
        ];

        $result = new Result('name', new stdClass, $defaultParams);

        $this->assertSame($defaultParams, $result->getParams());
        $this->assertSame($result, $result->setParams($newParams));
        $this->assertSame($newParams, $result->getParams());
    }

    /**
     * @test
     */
    public function testSetParam()
    {
        $defaultParams = [
            'default1' => mt_rand(),
            'default2' => mt_rand(),
            'default3' => mt_rand(),
        ];
        $newParam = mt_rand();

        $result = new Result('name', new stdClass, $defaultParams);

        $this->assertSame($defaultParams['default2'], $result->getParam('default2'));
        $this->assertSame($result, $result->setParam('new_param', $newParam));
        $this->assertSame($newParam, $result->getParam('new_param'));
    }

    /**
     * @test
     */
    public function testGetTraget()
    {
        $obj = new stdClass;
        $result = new Result('name', $obj);

        $this->assertSame($obj, $result->getTarget());
    }

    /**
     * @test
     */
    public function testConstructorEmptyNameException()
    {
        $this->setExpectedException(Exception::class);
        $result = new Result('', new stdClass);
    }

    /**
     * @test
     */
    public function testConstructorNonObjectTargetException()
    {
        $this->setExpectedException(Exception::class);
        $result = new Result('name', null);
    }
}
