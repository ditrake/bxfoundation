<?php

namespace creative\foundation\tests\lib\routing\rule;

class RegexpTest extends \PHPUnit_Framework_TestCase
{
    public function testAttachFiltersInConstructor()
    {
        $filter = $this->getMockBuilder('\creative\foundation\routing\filter\Header')
            ->disableOriginalConstructor()
            ->setMethods(['attachTo'])
            ->getMock();

        $filter->expects($this->once())
            ->method('attachTo');

        $rule = new \creative\foundation\routing\rule\Regexp('test', [$filter]);
    }


    public function testEmptyRegexpConstructorException()
    {
        $this->setExpectedException('\creative\foundation\routing\Exception');
        $rule = new \creative\foundation\routing\rule\Regexp(null);
    }

    public function testParse()
    {
        $id = (string) mt_rand();
        $code = 'qwe';
        $truePath = "/test/{$id}/{$code}/";
        $requestTrue = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $requestTrue->method('getPath')
            ->will($this->returnValue($truePath));

        $falsePath = '/test1/' . mt_rand() . '/qwe/' . mt_rand();
        $requestFalse = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();
        $requestFalse->method('getPath')
            ->will($this->returnValue($falsePath));

        $rule = new \creative\foundation\routing\rule\Regexp('/test/<id:\d+>/<code:[a-z]+>');

        $this->assertSame(
            ['id' => $id, 'code' => $code],
            $rule->parse($requestTrue)->getParams(),
            'parse method must checks url and returns parsed data'
        );

        $this->assertSame(
            null,
            $rule->parse($requestFalse),
            'prse method must checks url and returns null if it is wrong'
        );
    }

    public function testParseException()
    {
        $request = $this->getMockBuilder('\creative\foundation\request\Bitrix')
            ->disableOriginalConstructor()
            ->getMock();
        $rule = new \creative\foundation\routing\rule\Regexp('/test/ ewer/<code:[a-z]+>');
        $this->setExpectedException(
            '\creative\foundation\routing\Exception',
            ' ewer'
        );
        $rule->parse($request);
    }

    public function testAttachFilters()
    {
        $rule = new \creative\foundation\routing\rule\Regexp('test');

        $filter = $this->getMockBuilder('\creative\foundation\routing\filter\Header')
            ->disableOriginalConstructor()
            ->setMethods(['attachTo'])
            ->getMock();
        $filter->expects($this->once())
            ->method('attachTo')
            ->with($this->equalTo($rule));

        $rule->attachFilters([$filter]);
    }

    public function testAttachFiltersWrongClassException()
    {
        $rule = new \creative\foundation\routing\rule\Regexp('test');

        $filter = $this->getMockBuilder('\creative\foundation\routing\rule\Regexp')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectedException(
            '\creative\foundation\routing\Exception',
            'testKey'
        );
        $rule->attachFilters(['testKey' => $filter]);
    }
}
