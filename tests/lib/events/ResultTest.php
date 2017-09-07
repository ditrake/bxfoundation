<?php

namespace creative\foundation\tests\lib\events;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $result = new \creative\foundation\events\Result('name', new \StdClass);

        $this->assertSame(
            'name',
            $result->getName(),
            'getName method must returns name that was set in constructor'
        );
    }

    public function testFail()
    {
        $result = new \creative\foundation\events\Result('name', new \StdClass);

        $this->assertSame(
            true,
            $result->isSuccess(),
            'isSuccess method must returns true by default'
        );

        $this->assertSame(
            $result,
            $result->fail(),
            'fail method must returns it\'s object'
        );

        $this->assertSame(
            false,
            $result->isSuccess(),
            'fail method must sets isSuccess result to false'
        );
    }

    public function testSetParams()
    {
        $defaultParams = ['default1' => mt_rand(), 'default2' => mt_rand(), 'default3' => mt_rand()];
        $newParams = ['new1' => mt_rand(), 'new2' => mt_rand()];

        $result = new \creative\foundation\events\Result('name', new \StdClass, $defaultParams);

        $this->assertSame(
            $defaultParams,
            $result->getParams(),
            'getParams method must returns parameters that was set in constructor by default'
        );

        $this->assertSame(
            $result,
            $result->setParams($newParams),
            'setParams method must returns it\'s object'
        );

        $this->assertSame(
            $newParams,
            $result->getParams(),
            'setParams method must change params returned by getParams'
        );
    }

    public function testSetParam()
    {
        $defaultParams = ['default1' => mt_rand(), 'default2' => mt_rand(), 'default3' => mt_rand()];
        $newParam = mt_rand();

        $result = new \creative\foundation\events\Result('name', new \StdClass, $defaultParams);

        $this->assertSame(
            $defaultParams['default2'],
            $result->getParam('default2'),
            'getParam method must returns parametersthat was set in constructor by default'
        );

        $this->assertSame(
            $result,
            $result->setParam('new_param', $newParam),
            'setParam method must returns it\'s object'
        );

        $this->assertSame(
            $newParam,
            $result->getParam('new_param'),
            'setParam method must change param returned by getParam'
        );
    }

    public function testGetTraget()
    {
        $obj = new \StdClass;
        $result = new \creative\foundation\events\Result('name', $obj);

        $this->assertSame(
            $obj,
            $result->getTarget(),
            'getTarget method must return target setted by constructor'
        );
    }

    public function testConstructorEmptyNameException()
    {
        $this->setExpectedException('\creative\foundation\events\Exception');
        $result = new \creative\foundation\events\Result('', new \StdClass);
    }

    public function testConstructorNonObjectTargetException()
    {
        $this->setExpectedException('\creative\foundation\events\Exception');
        $result = new \creative\foundation\events\Result('name', null);
    }
}
