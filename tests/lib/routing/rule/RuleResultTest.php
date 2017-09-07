<?php

namespace creative\foundation\tests\lib\routing\rule;

class RuleResultTest extends \PHPUnit_Framework_TestCase
{
    public function testSetParams()
    {
        $defaultParams = ['default1' => mt_rand(), 'default2' => mt_rand(), 'default3' => mt_rand()];
        $newParams = ['new1' => mt_rand(), 'new2' => mt_rand()];

        $result = new \creative\foundation\routing\rule\RuleResult($defaultParams);

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

        $result = new \creative\foundation\routing\rule\RuleResult($defaultParams);

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
}
