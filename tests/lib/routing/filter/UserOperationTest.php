<?php

namespace marvin255\bxfoundation\tests\lib\routing\filter;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\routing\filter\UserOperation;
use marvin255\bxfoundation\events\EventableInterface;
use marvin255\bxfoundation\events\ResultInterface;
use marvin255\bxfoundation\services\user\UserInterface;

class UserOperationTest extends BaseCase
{
    /**
     * @test
     */
    public function testEmptyConstructorException()
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $this->setExpectedException(Exception::class);
        $filter = new UserOperation($user, []);
    }

    /**
     * @test
     */
    public function testAttachTo()
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $filter = new UserOperation($user, 'test');

        $eventable = $this->getMockBuilder(EventableInterface::class)
            ->getMock();
        $eventable->expects($this->once())
            ->method('attachEventCallback')
            ->with(
                $this->equalTo('onAfterRouteParsing'),
                $this->equalTo([$filter, 'filter'])
            );

        $this->assertSame($filter, $filter->attachTo($eventable));
    }

    /**
     * @test
     */
    public function testFilter()
    {
        $operation1 = 'operation_1_' . mt_rand();
        $operation2 = 'operation_1_' . mt_rand();

        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->method('canDoOperation')->will($this->returnCallback(function ($name) use ($operation1) {
            return $name === $operation1;
        }));

        $result = $this->getMockBuilder(ResultInterface::class)->getMock();
        $result->expects($this->once())->method('fail');

        $filter = new UserOperation($user, [$operation1, $operation2]);
        $filter->filter($result);
    }
}
