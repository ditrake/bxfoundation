<?php

namespace marvin255\bxfoundation\tests\lib\routing\rule;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\routing\rule\RuleResult;

class RuleResultTest extends BaseCase
{
    /**
     * @test
     */
    public function testSetParams()
    {
        $defaultParams = ['default1' => mt_rand(), 'default2' => mt_rand(), 'default3' => mt_rand()];
        $newParams = ['new1' => mt_rand(), 'new2' => mt_rand()];

        $result = new RuleResult($defaultParams);

        $this->assertSame($defaultParams, $result->getParams());
        $this->assertSame($result, $result->setParams($newParams));
        $this->assertSame($newParams['new1'], $result->getParam('new1'));
        $this->assertSame($newParams, $result->getParams());
    }

    /**
     * @test
     */
    public function testSetParam()
    {
        $defaultParams = ['default1' => mt_rand(), 'default2' => mt_rand(), 'default3' => mt_rand()];
        $newParam = mt_rand();

        $result = new RuleResult($defaultParams);

        $this->assertSame($defaultParams['default2'], $result->getParam('default2'));
        $this->assertSame($result, $result->setParam('new_param', $newParam));
        $this->assertSame($newParam, $result->getParam('new_param'));
    }
}
