<?php

namespace marvin255\bxfoundation\tests\lib\routing\rule;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\routing\rule\Regexp;
use marvin255\bxfoundation\request\Bitrix as Request;

class RegexpTest extends BaseCase
{
    /**
     * @test
     */
    public function testEmptyRegexpConstructorException()
    {
        $this->setExpectedException(Exception::class);
        $rule = new Regexp(null);
    }

    /**
     * @test
     */
    public function testParse()
    {
        $id = (string) mt_rand();
        $code = 'qwe';
        $roteParams = ['id' => $id, 'code' => $code];

        $truePath = "/test/{$id}/before_{$code}_after/";
        $requestTrue = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $requestTrue->method('getPath')
            ->will($this->returnValue($truePath));

        $falsePath = '/test1/' . mt_rand() . '/qwe/' . mt_rand();
        $requestFalse = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $requestFalse->method('getPath')
            ->will($this->returnValue($falsePath));

        $rule = new Regexp('/test/<id:\d+>/before_<code:[a-z]+>_after');

        $this->assertSame($roteParams, $rule->parse($requestTrue)->getParams());
        $this->assertSame(null, $rule->parse($requestFalse));
    }

    /**
     * @test
     */
    public function testParseException()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $rule = new Regexp('/test/ ewer/<code:[a-z]+>');

        $this->setExpectedException(Exception::class, ' ewer');
        $rule->parse($request);
    }

    /**
     * @test
     */
    public function testCreateUrl()
    {
        $id = mt_rand();
        $code = 'code_' . mt_rand();

        $rule = new Regexp('/test/<id:\d+>/before_<code:[a-z0-9_]+>_after');

        $this->assertSame(
            "/test/{$id}/before_{$code}_after",
            $rule->createUrl(['id' => $id, 'code' => $code])
        );
    }

    /**
     * @test
     */
    public function testCreateUrlEmptyParamException()
    {
        $code = 'code_' . mt_rand();

        $rule = new Regexp('/test/<id:\d+>/before_<code:[a-z0-9_]+>_after');

        $this->setExpectedException(Exception::class, 'id');
        $rule->createUrl(['code' => $code]);
    }

    /**
     * @test
     */
    public function testCreateUrlWrongParamTypeException()
    {
        $id = 'string_' . mt_rand();
        $code = 'code_' . mt_rand();

        $rule = new Regexp('/test/<id:\d+>/before_<code:[a-z]+>_after');

        $this->setExpectedException(Exception::class, 'id');
        $rule->createUrl(['code' => $code, 'id' => $id]);
    }
}
