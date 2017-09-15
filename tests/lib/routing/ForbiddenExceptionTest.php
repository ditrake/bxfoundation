<?php

namespace marvin255\bxfoundation\tests\lib\routing;

class ForbiddenExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHttpStatus()
    {
        $exception = new \marvin255\bxfoundation\routing\ForbiddenException;

        $this->assertSame(
            '403 Forbidden',
            $exception->getHttpStatus()
        );
    }
}
