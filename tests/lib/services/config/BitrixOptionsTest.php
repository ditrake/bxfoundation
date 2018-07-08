<?php

namespace marvin255\bxfoundation\tests\lib\services\config;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\services\config\BitrixOptions;
use Bitrix\Main\Config\Option;

class BitrixOptionsTest extends BaseCase
{
    /**
     * @test
     */
    public function testGet()
    {
        $default = 'default_value_' . mt_rand();
        $options = [
            'module_1' => [
                'option' => 'value_1_1_' . mt_rand(),
                'option_2' => 'value_1_2_' . mt_rand(),
            ],
            'module_2' => [
                'option' => 'value_1_1_' . mt_rand(),
            ],
        ];
        Option::$settedOptions = $options;

        $cnf = new BitrixOptions;

        $this->assertSame($options['module_1']['option'], $cnf->get('module_1', 'option'));
        $this->assertSame($options['module_2']['option'], $cnf->get('module_2', 'option'));
        $this->assertSame($default, $cnf->get('module_3', 'option', $default));
    }

    /**
     * @test
     */
    public function testSet()
    {
        $newValue = 'new_value_' . mt_rand();
        $options = [
            'module_1' => [
                'option' => 'value_1_1_' . mt_rand(),
                'option_2' => 'value_1_2_' . mt_rand(),
            ],
        ];
        Option::$settedOptions = $options;

        $cnf = new BitrixOptions;
        $cnf->set('module_1', 'option', $newValue);

        $this->assertSame($newValue, $cnf->get('module_1', 'option'));
        $this->assertSame($options['module_1']['option_2'], $cnf->get('module_1', 'option_2'));
    }
}
